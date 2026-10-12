<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Driver;
use App\Models\Mission;
use App\Models\MissionStop;
use App\Models\Product;
use App\Models\Site;
use App\Models\Truck;
use App\Services\MissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class MissionController extends Controller
{
    public function index()
    {
        $missions = Mission::with([
            'driver',
            'truck',
            'departureSite',
        ])
            ->latest('mission_date')
            ->latest('id')
            ->paginate(10);

        return view('missions.index', compact('missions'));
    }

    public function create()
    {
        $drivers = Driver::where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $trucks = Truck::query()->get();

        $sites = Site::where('status', 'active')
            ->orderBy('name')
            ->get();

        $clients = Client::where('status', 'active')
            ->orderBy('name')
            ->get();

        $products = Product::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('missions.create', compact(
            'drivers',
            'trucks',
            'sites',
            'clients',
            'products'
        ));
    }

    public function store(
        Request $request,
        MissionService $missionService
    ) {
        $validated = $request->validate([
            'reference' => [
                'required',
                'string',
                'max:255',
                'unique:missions,reference',
            ],
            'truck_id' => ['required', 'integer', 'exists:trucks,id'],
            'driver_id' => ['required', 'integer', 'exists:drivers,id'],
            'departure_site_id' => ['required', 'integer', 'exists:sites,id'],
            'mission_date' => ['required', 'date'],
            'departure_time' => ['nullable', 'date'],
            'mission_type' => [
                'required',
                Rule::in(['delivery', 'transfer', 'return', 'mixed']),
            ],
            'status' => [
                'required',
                Rule::in(['planned', 'in_progress', 'completed', 'cancelled']),
            ],
            'total_distance' => ['nullable', 'numeric', 'min:0'],
            'total_quantity' => ['nullable', 'numeric', 'min:0'],
            'transport_cost' => ['nullable', 'numeric', 'min:0'],
            'fuel_cost' => ['nullable', 'numeric', 'min:0'],
            'driver_cost' => ['nullable', 'numeric', 'min:0'],
            'other_cost' => ['nullable', 'numeric', 'min:0'],
            'total_revenue' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'stops' => ['nullable', 'array', 'max:30'],
            'stops.*.stop_type' => [
                'required',
                Rule::in([
                    'loading',
                    'delivery',
                    'transfer',
                    'return',
                    'unloading',
                ]),
            ],
            'stops.*.site_id' => [
                'nullable',
                'integer',
                'exists:sites,id',
            ],
            'stops.*.client_id' => [
                'nullable',
                'integer',
                'exists:clients,id',
            ],
            'stops.*.product_id' => [
                'nullable',
                'integer',
                'exists:products,id',
            ],
            'stops.*.quantity' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999',
                'regex:/^\d+(\.\d{1,3})?$/',
            ],
            'stops.*.unit' => ['required', 'string', 'max:50'],
            'stops.*.distance_from_previous' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'stops.*.notes' => ['nullable', 'string'],
        ]);

        $stops = $validated['stops'] ?? [];

        foreach ($stops as $index => $stop) {
            $quantity = $stop['quantity'] ?? 0;

            if ($stop['stop_type'] === 'loading') {
                $stops[$index]['quantity'] = (float) $quantity;
            } elseif (!is_numeric($quantity) || (float) $quantity <= 0) {
                throw ValidationException::withMessages([
                    "stops.$index.quantity" =>
                        'La quantité doit être supérieure à zéro pour cet arrêt.',
                ]);
            }

            if (
                $stop['stop_type'] === 'loading'
                && empty($stop['site_id'])
            ) {
                throw ValidationException::withMessages([
                    "stops.$index.site_id" =>
                        'Le site est obligatoire pour un chargement.',
                ]);
            }

            if (
                in_array(
                    $stop['stop_type'],
                    ['loading', 'delivery', 'unloading'],
                    true
                )
                && empty($stop['product_id'])
            ) {
                throw ValidationException::withMessages([
                    "stops.$index.product_id" =>
                        'Le produit est obligatoire pour ce type d’arrêt.',
                ]);
            }
        }

        DB::transaction(function () use (
            $validated,
            $stops,
            $missionService
        ) {
            $mission = new Mission(
                collect($validated)->except([
                    'stops',
                    'departure_time',
                    'status',
                ])->all()
            );

            $mission->status = 'planned';
            $mission->departure_time = null;
            $mission->return_time = null;

            $mission->total_distance = $validated['total_distance'] ?? 0;
            $mission->total_quantity = $validated['total_quantity'] ?? 0;
            $mission->transport_cost = $validated['transport_cost'] ?? 0;
            $mission->fuel_cost = $validated['fuel_cost'] ?? 0;
            $mission->driver_cost = $validated['driver_cost'] ?? 0;
            $mission->other_cost = $validated['other_cost'] ?? 0;
            $mission->total_revenue = $validated['total_revenue'] ?? 0;

            $mission->total_cost =
                (float) $mission->transport_cost
                + (float) $mission->fuel_cost
                + (float) $mission->driver_cost
                + (float) $mission->other_cost;

            $mission->profit =
                (float) $mission->total_revenue
                - (float) $mission->total_cost;

            $mission->save();

            foreach ($stops as $index => $stopData) {
                $stopType = $stopData['stop_type'];

                $loadingStatus = $stopType === 'loading'
                    ? 'pending'
                    : 'not_applicable';

                $deliveryStatus = in_array(
                    $stopType,
                    ['delivery', 'unloading'],
                    true
                )
                    ? 'pending'
                    : 'not_applicable';

                $mission->stops()->create([
                    'stop_order' => $index + 1,
                    'stop_type' => $stopType,
                    'site_id' => $stopData['site_id'] ?? null,
                    'client_id' => $stopData['client_id'] ?? null,
                    'product_id' => $stopData['product_id'] ?? null,
                    'quantity' => $stopData['quantity'] ?? 0,
                    'delivered_quantity' => 0,
                    'unit' => $stopData['unit'],
                    'distance_from_previous' =>
                        $stopData['distance_from_previous'] ?? 0,
                    'loading_status' => $loadingStatus,
                    'delivery_status' => $deliveryStatus,
                    'notes' => $stopData['notes'] ?? null,
                ]);
            }

            $missionService->calculateCosts($mission);
        });

        return redirect()
            ->route('missions.index')
            ->with('success', 'Mission créée avec succès.');
    }

    public function show(Mission $mission)
    {
        $mission->load([
            'driver',
            'truck',
            'departureSite',
            'returnSite',
            'stops.site',
            'stops.client',
            'stops.product',
        ]);

        $sites = Site::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('missions.show', compact('mission', 'sites'));
    }

    public function start(
        Mission $mission,
        MissionService $missionService
    ) {
        try {
            $missionService->startMission($mission);
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('missions.show', $mission)
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('missions.show', $mission)
            ->with('success', 'Mission démarrée avec succès.');
    }

    public function edit(Mission $mission)
    {
        $drivers = Driver::where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $trucks = Truck::query()->get();

        $sites = Site::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('missions.edit', compact(
            'mission',
            'drivers',
            'trucks',
            'sites'
        ));
    }

    public function update(
        Request $request,
        Mission $mission,
        MissionService $missionService
    ) {
        $validated = $request->validate([
            'reference' => [
                'required',
                'string',
                'max:255',
                Rule::unique('missions', 'reference')->ignore($mission->id),
            ],
            'truck_id' => ['required', 'integer', 'exists:trucks,id'],
            'driver_id' => ['required', 'integer', 'exists:drivers,id'],
            'departure_site_id' => ['required', 'integer', 'exists:sites,id'],
            'mission_date' => ['required', 'date'],
            'departure_time' => ['nullable', 'date'],
            'mission_type' => [
                'required',
                Rule::in(['delivery', 'transfer', 'return', 'mixed']),
            ],
            'status' => [
                'required',
                Rule::in(['planned', 'in_progress', 'completed', 'cancelled']),
            ],
            'total_distance' => ['nullable', 'numeric', 'min:0'],
            'total_quantity' => ['nullable', 'numeric', 'min:0'],
            'transport_cost' => ['nullable', 'numeric', 'min:0'],
            'fuel_cost' => ['nullable', 'numeric', 'min:0'],
            'driver_cost' => ['nullable', 'numeric', 'min:0'],
            'other_cost' => ['nullable', 'numeric', 'min:0'],
            'total_revenue' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        unset($validated['status'], $validated['departure_time']);

        $mission->fill($validated);

        $mission->total_distance = $validated['total_distance'] ?? 0;
        $mission->total_quantity = $validated['total_quantity'] ?? 0;
        $mission->transport_cost = $validated['transport_cost'] ?? 0;
        $mission->fuel_cost = $validated['fuel_cost'] ?? 0;
        $mission->driver_cost = $validated['driver_cost'] ?? 0;
        $mission->other_cost = $validated['other_cost'] ?? 0;
        $mission->total_revenue = $validated['total_revenue'] ?? 0;

        DB::transaction(function () use ($mission, $missionService) {
            $mission->save();
            $missionService->calculateCosts($mission);
        });

        return redirect()
            ->route('missions.index')
            ->with('success', 'Mission modifiée avec succès.');
    }

    public function loadStop(
        Request $request,
        Mission $mission,
        MissionStop $stop,
        MissionService $missionService
    ) {
        if ((int) $stop->mission_id !== (int) $mission->id) {
            abort(404);
        }

        $validated = $request->validate([
            'quantity' => [
                'required',
                'numeric',
                'gt:0',
                'max:999999999',
                'regex:/^\d+(\.\d{1,3})?$/',
            ],
        ]);

        try {
            $missionService->loadMissionStop(
                $stop,
                (float) $validated['quantity'],
                Auth::id()
            );
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('missions.show', $mission)
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('missions.show', $mission)
            ->with('success', 'Chargement effectué avec succès.');
    }

    public function deliverStop(
        Request $request,
        Mission $mission,
        MissionStop $stop,
        MissionService $missionService
    ) {
        if ((int) $stop->mission_id !== (int) $mission->id) {
            abort(404);
        }

        $validated = $request->validate([
            'delivered_quantity' => [
                'required',
                'numeric',
                'gt:0',
                'max:999999999',
                'regex:/^\d+(\.\d{1,3})?$/',
            ],
        ]);

        try {
            $missionService->deliverMissionStop(
                $stop,
                (float) $validated['delivered_quantity']
            );
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('missions.show', $mission)
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('missions.show', $mission)
            ->with('success', 'Quantité livrée enregistrée avec succès.');
    }

    public function returnStock(
        Request $request,
        Mission $mission,
        MissionService $missionService
    ) {
        $validated = $request->validate([
            'return_site_id' => [
                'required',
                'integer',
                'exists:sites,id',
            ],
            'returned_quantity' => [
                'required',
                'numeric',
                'gt:0',
                'max:999999999',
                'regex:/^\d+(\.\d{1,3})?$/',
            ],
        ]);

        try {
            $missionService->returnMissionStock(
                $mission,
                (int) $validated['return_site_id'],
                (float) $validated['returned_quantity'],
                Auth::id()
            );
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('missions.show', $mission)
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('missions.show', $mission)
            ->with('success', 'Retour de marchandise enregistré avec succès.');
    }

    public function complete(
        Mission $mission,
        MissionService $missionService
    ) {
        try {
            $missionService->completeMission($mission);
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('missions.show', $mission)
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('missions.show', $mission)
            ->with('success', 'Mission terminée avec succès.');
    }

    public function destroy(Mission $mission)
    {
        if ($mission->stops()->exists()) {
            return redirect()
                ->route('missions.index')
                ->with(
                    'error',
                    'Impossible de supprimer une mission qui contient des arrêts.'
                );
        }

        $mission->delete();

        return redirect()
            ->route('missions.index')
            ->with('success', 'Mission supprimée avec succès.');
    }
}

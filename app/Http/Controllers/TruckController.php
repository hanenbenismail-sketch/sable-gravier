<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TruckController extends Controller
{
    public function index()
    {
        $trucks = Truck::latest()->paginate(10);

        return view('trucks.index', compact('trucks'));
    }

    public function create()
    {
        return view('trucks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'registration_number' => [
                'required',
                'string',
                'max:255',
                'unique:trucks,registration_number',
            ],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'capacity' => ['required', 'numeric', 'gt:0'],
            'unit' => ['required', Rule::in(['tonne', 'm3'])],
            'current_kilometer' => ['required', 'integer', 'min:0'],
            'fuel_consumption' => ['nullable', 'numeric', 'min:0'],
            'status' => [
                'required',
                Rule::in(['AVAILABLE', 'IN_MISSION', 'MAINTENANCE', 'INACTIVE']),
            ],
            'insurance_expiry' => ['nullable', 'date'],
            'technical_visit_expiry' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        Truck::create($validated);

        return redirect()
            ->route('trucks.index')
            ->with('success', 'Camion ajouté avec succès.');
    }

    public function show(Truck $truck)
    {
        return view('trucks.show', compact('truck'));
    }

    public function edit(Truck $truck)
    {
        return view('trucks.edit', compact('truck'));
    }

    public function update(Request $request, Truck $truck)
    {
        $validated = $request->validate([
            'registration_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('trucks', 'registration_number')->ignore($truck->id),
            ],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'capacity' => ['required', 'numeric', 'gt:0'],
            'unit' => ['required', Rule::in(['tonne', 'm3'])],
            'current_kilometer' => ['required', 'integer', 'min:0'],
            'fuel_consumption' => ['nullable', 'numeric', 'min:0'],
            'status' => [
                'required',
                Rule::in(['AVAILABLE', 'IN_MISSION', 'MAINTENANCE', 'INACTIVE']),
            ],
            'insurance_expiry' => ['nullable', 'date'],
            'technical_visit_expiry' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $truck->update($validated);

        return redirect()
            ->route('trucks.index')
            ->with('success', 'Camion modifié avec succès.');
    }

    public function destroy(Truck $truck)
    {
        if ($truck->missions()->exists()) {
            return redirect()
                ->route('trucks.index')
                ->with('error', 'Impossible de supprimer ce camion : il est lié à une ou plusieurs missions.');
        }

        $truck->delete();

        return redirect()
            ->route('trucks.index')
            ->with('success', 'Camion supprimé avec succès.');
    }
}

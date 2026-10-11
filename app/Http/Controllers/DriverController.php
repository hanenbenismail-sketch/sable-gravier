<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::latest()->paginate(10);

        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'license_number' => [
                'required', 'string', 'max:255', 'unique:drivers,license_number',
            ],
            'license_category' => ['nullable', 'string', 'max:255'],
            'salary_type' => [
                'required',
                Rule::in(['monthly', 'daily', 'per_trip', 'per_km']),
            ],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'salary_per_day' => ['required', 'numeric', 'min:0'],
            'salary_per_trip' => ['required', 'numeric', 'min:0'],
            'salary_per_km' => ['required', 'numeric', 'min:0'],
            'bonus' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'notes' => ['nullable', 'string'],
        ]);

        Driver::create($validated);

        return redirect()
            ->route('drivers.index')
            ->with('success', 'Chauffeur ajouté avec succès.');
    }

    public function show(Driver $driver)
    {
        return view('drivers.show', compact('driver'));
    }

    public function edit(Driver $driver)
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'license_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('drivers', 'license_number')->ignore($driver->id),
            ],
            'license_category' => ['nullable', 'string', 'max:255'],
            'salary_type' => [
                'required',
                Rule::in(['monthly', 'daily', 'per_trip', 'per_km']),
            ],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'salary_per_day' => ['required', 'numeric', 'min:0'],
            'salary_per_trip' => ['required', 'numeric', 'min:0'],
            'salary_per_km' => ['required', 'numeric', 'min:0'],
            'bonus' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'notes' => ['nullable', 'string'],
        ]);

        $driver->update($validated);

        return redirect()
            ->route('drivers.index')
            ->with('success', 'Chauffeur modifié avec succès.');
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();

        return redirect()
            ->route('drivers.index')
            ->with('success', 'Chauffeur supprimé avec succès.');
    }
}

@extends('layouts.app')

@section('title', 'Modifier un camion - Sable & Gravier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold mb-1">Modifier un camion</h1>
        <p class="text-muted mb-0">{{ $truck->registration_number }}</p>
    </div>

    <a href="{{ route('trucks.index') }}" class="btn btn-outline-secondary">
        Retour à la liste
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('trucks.update', $truck) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Immatriculation *</label>
                    <input type="text" name="registration_number" class="form-control"
                           value="{{ old('registration_number', $truck->registration_number) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Marque</label>
                    <input type="text" name="brand" class="form-control"
                           value="{{ old('brand', $truck->brand) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Modèle</label>
                    <input type="text" name="model" class="form-control"
                           value="{{ old('model', $truck->model) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Capacité *</label>
                    <input type="number" name="capacity" class="form-control"
                           value="{{ old('capacity', $truck->capacity) }}"
                           min="0.001" step="0.001" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Unité *</label>
                    <select name="unit" class="form-select" required>
                        <option value="tonne" @selected(old('unit', $truck->unit) === 'tonne')>Tonne</option>
                        <option value="m3" @selected(old('unit', $truck->unit) === 'm3')>m³</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kilométrage actuel *</label>
                    <input type="number" name="current_kilometer" class="form-control"
                           value="{{ old('current_kilometer', $truck->current_kilometer) }}"
                           min="0" step="1" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Consommation carburant (L/100 km)</label>
                    <input type="number" name="fuel_consumption" class="form-control"
                           value="{{ old('fuel_consumption', $truck->fuel_consumption) }}"
                           min="0" step="0.01">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Statut *</label>
                    <select name="status" class="form-select" required>
                        <option value="AVAILABLE" @selected(old('status', $truck->status) === 'AVAILABLE')>Disponible</option>
                        <option value="IN_MISSION" @selected(old('status', $truck->status) === 'IN_MISSION')>En mission</option>
                        <option value="MAINTENANCE" @selected(old('status', $truck->status) === 'MAINTENANCE')>En maintenance</option>
                        <option value="INACTIVE" @selected(old('status', $truck->status) === 'INACTIVE')>Inactif</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Expiration assurance</label>
                    <input type="date" name="insurance_expiry" class="form-control"
                           value="{{ old('insurance_expiry', $truck->insurance_expiry?->format('Y-m-d')) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Expiration visite technique</label>
                    <input type="date" name="technical_visit_expiry" class="form-control"
                           value="{{ old('technical_visit_expiry', $truck->technical_visit_expiry?->format('Y-m-d')) }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $truck->notes) }}</textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Enregistrer les modifications
                </button>

                <a href="{{ route('trucks.index') }}" class="btn btn-secondary">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

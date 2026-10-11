@extends('layouts.app')

@section('title', 'Ajouter un camion - Sable & Gravier')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">Ajouter un camion</h1>
            <p class="text-muted mb-0">Enregistrer un nouveau camion de transport</p>
        </div>

        <a href="{{ route('trucks.index') }}" class="btn btn-secondary">
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
            <form action="{{ route('trucks.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="registration_number">Immatriculation *</label>
                        <input class="form-control" type="text" id="registration_number"
                               name="registration_number" value="{{ old('registration_number') }}" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="brand">Marque</label>
                        <input class="form-control" type="text" id="brand"
                               name="brand" value="{{ old('brand') }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="model">Modèle</label>
                        <input class="form-control" type="text" id="model"
                               name="model" value="{{ old('model') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="capacity">Capacité *</label>
                        <input class="form-control" type="number" id="capacity"
                               name="capacity" step="0.001" min="0.001"
                               value="{{ old('capacity') }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="unit">Unité *</label>
                        <select class="form-select" id="unit" name="unit" required>
                            <option value="tonne" @selected(old('unit', 'tonne') === 'tonne')>Tonne</option>
                            <option value="m3" @selected(old('unit') === 'm3')>m³</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="current_kilometer">Kilométrage actuel *</label>
                        <input class="form-control" type="number" id="current_kilometer"
                               name="current_kilometer" min="0"
                               value="{{ old('current_kilometer', 0) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="fuel_consumption">Consommation (L/100 km)</label>
                        <input class="form-control" type="number" id="fuel_consumption"
                               name="fuel_consumption" min="0" step="0.01"
                               value="{{ old('fuel_consumption') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="status">Statut *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="AVAILABLE" @selected(old('status', 'AVAILABLE') === 'AVAILABLE')>Disponible</option>
                            <option value="IN_MISSION" @selected(old('status') === 'IN_MISSION')>En mission</option>
                            <option value="MAINTENANCE" @selected(old('status') === 'MAINTENANCE')>En maintenance</option>
                            <option value="INACTIVE" @selected(old('status') === 'INACTIVE')>Inactif</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="insurance_expiry">Expiration assurance</label>
                        <input class="form-control" type="date" id="insurance_expiry"
                               name="insurance_expiry" value="{{ old('insurance_expiry') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="technical_visit_expiry">Expiration visite technique</label>
                        <input class="form-control" type="date" id="technical_visit_expiry"
                               name="technical_visit_expiry" value="{{ old('technical_visit_expiry') }}">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label" for="notes">Remarques</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Enregistrer le camion</button>
                <a href="{{ route('trucks.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Modifier un chauffeur</h1>
            <p>Modifier les informations du chauffeur.</p>
        </div>

        <a href="{{ route('drivers.index') }}" class="btn btn-secondary">
            Retour à la liste
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Veuillez corriger les erreurs suivantes :</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('drivers.update', $driver) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header">
                Informations personnelles
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">Prénom *</label>
                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            class="form-control"
                            value="{{ old('first_name', $driver->first_name) }}"
                            required
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Nom *</label>
                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            class="form-control"
                            value="{{ old('last_name', $driver->last_name) }}"
                            required
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            class="form-control"
                            value="{{ old('phone', $driver->phone) }}"
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="license_number" class="form-label">
                            Numéro de permis *
                        </label>
                        <input
                            type="text"
                            id="license_number"
                            name="license_number"
                            class="form-control"
                            value="{{ old('license_number', $driver->license_number) }}"
                            required
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="license_category" class="form-label">
                            Catégorie du permis
                        </label>
                        <input
                            type="text"
                            id="license_category"
                            name="license_category"
                            class="form-control"
                            value="{{ old('license_category', $driver->license_category) }}"
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Statut *</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="active"
                                {{ old('status', $driver->status) === 'active' ? 'selected' : '' }}>
                                Actif
                            </option>
                            <option value="inactive"
                                {{ old('status', $driver->status) === 'inactive' ? 'selected' : '' }}>
                                Inactif
                            </option>
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea
                            id="notes"
                            name="notes"
                            class="form-control"
                            rows="3"
                        >{{ old('notes', $driver->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                Rémunération
            </div>

            <div class="card-body">
                <div class="mb-3">
                    <label for="salary_type" class="form-label">
                        Type de salaire *
                    </label>
                    <select
                        id="salary_type"
                        name="salary_type"
                        class="form-select"
                        required
                    >
                        <option value="monthly"
                            {{ old('salary_type', $driver->salary_type) === 'monthly' ? 'selected' : '' }}>
                            Mensuel
                        </option>
                        <option value="daily"
                            {{ old('salary_type', $driver->salary_type) === 'daily' ? 'selected' : '' }}>
                            Journalier
                        </option>
                        <option value="per_trip"
                            {{ old('salary_type', $driver->salary_type) === 'per_trip' ? 'selected' : '' }}>
                            Par trajet
                        </option>
                        <option value="per_km"
                            {{ old('salary_type', $driver->salary_type) === 'per_km' ? 'selected' : '' }}>
                            Par kilomètre
                        </option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="base_salary" class="form-label">
                            Salaire de base (DT) *
                        </label>
                        <input
                            type="number"
                            id="base_salary"
                            name="base_salary"
                            class="form-control"
                            min="0"
                            step="0.001"
                            value="{{ old('base_salary', $driver->base_salary) }}"
                            required
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="salary_per_day" class="form-label">
                            Salaire par jour (DT) *
                        </label>
                        <input
                            type="number"
                            id="salary_per_day"
                            name="salary_per_day"
                            class="form-control"
                            min="0"
                            step="0.001"
                            value="{{ old('salary_per_day', $driver->salary_per_day) }}"
                            required
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="salary_per_trip" class="form-label">
                            Salaire par trajet (DT) *
                        </label>
                        <input
                            type="number"
                            id="salary_per_trip"
                            name="salary_per_trip"
                            class="form-control"
                            min="0"
                            step="0.001"
                            value="{{ old('salary_per_trip', $driver->salary_per_trip) }}"
                            required
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="salary_per_km" class="form-label">
                            Salaire par kilomètre (DT) *
                        </label>
                        <input
                            type="number"
                            id="salary_per_km"
                            name="salary_per_km"
                            class="form-control"
                            min="0"
                            step="0.001"
                            value="{{ old('salary_per_km', $driver->salary_per_km) }}"
                            required
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="bonus" class="form-label">Prime (DT) *</label>
                        <input
                            type="number"
                            id="bonus"
                            name="bonus"
                            class="form-control"
                            min="0"
                            step="0.001"
                            value="{{ old('bonus', $driver->bonus) }}"
                            required
                        >
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            Enregistrer les modifications
        </button>

        <a href="{{ route('drivers.index') }}" class="btn btn-secondary">
            Annuler
        </a>
    </form>
</div>
@endsection

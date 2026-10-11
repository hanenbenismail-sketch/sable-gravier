@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Ajouter un chauffeur</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('drivers.store') }}" method="POST">
        @csrf

        <h4 class="mb-3">Informations personnelles</h4>

        <div class="mb-3">
            <label for="first_name" class="form-label">Prénom *</label>
            <input type="text" name="first_name" id="first_name"
                   class="form-control" value="{{ old('first_name') }}" required>
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">Nom *</label>
            <input type="text" name="last_name" id="last_name"
                   class="form-control" value="{{ old('last_name') }}" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Téléphone</label>
            <input type="text" name="phone" id="phone"
                   class="form-control" value="{{ old('phone') }}">
        </div>

        <div class="mb-3">
            <label for="license_number" class="form-label">Numéro du permis *</label>
            <input type="text" name="license_number" id="license_number"
                   class="form-control" value="{{ old('license_number') }}" required>
        </div>

        <div class="mb-3">
            <label for="license_category" class="form-label">Catégorie du permis</label>
            <input type="text" name="license_category" id="license_category"
                   class="form-control" value="{{ old('license_category') }}"
                   placeholder="Ex. B, C, CE">
        </div>

        <h4 class="mt-4 mb-3">Rémunération</h4>

        <div class="mb-3">
            <label for="salary_type" class="form-label">Mode de rémunération *</label>
            <select name="salary_type" id="salary_type" class="form-select" required>
                <option value="monthly" {{ old('salary_type', 'monthly') === 'monthly' ? 'selected' : '' }}>
                    Mensuel
                </option>
                <option value="daily" {{ old('salary_type') === 'daily' ? 'selected' : '' }}>
                    Journalier
                </option>
                <option value="per_trip" {{ old('salary_type') === 'per_trip' ? 'selected' : '' }}>
                    Par trajet
                </option>
                <option value="per_km" {{ old('salary_type') === 'per_km' ? 'selected' : '' }}>
                    Par kilomètre
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label for="base_salary" class="form-label">Salaire de base (DT) *</label>
            <input type="number" step="0.001" min="0"
                   name="base_salary" id="base_salary"
                   class="form-control" value="{{ old('base_salary', '0') }}" required>
        </div>

        <div class="mb-3">
            <label for="salary_per_day" class="form-label">Salaire par jour (DT) *</label>
            <input type="number" step="0.001" min="0"
                   name="salary_per_day" id="salary_per_day"
                   class="form-control" value="{{ old('salary_per_day', '0') }}" required>
        </div>

        <div class="mb-3">
            <label for="salary_per_trip" class="form-label">Salaire par trajet (DT) *</label>
            <input type="number" step="0.001" min="0"
                   name="salary_per_trip" id="salary_per_trip"
                   class="form-control" value="{{ old('salary_per_trip', '0') }}" required>
        </div>

        <div class="mb-3">
            <label for="salary_per_km" class="form-label">Salaire par kilomètre (DT) *</label>
            <input type="number" step="0.001" min="0"
                   name="salary_per_km" id="salary_per_km"
                   class="form-control" value="{{ old('salary_per_km', '0') }}" required>
        </div>

        <div class="mb-3">
            <label for="bonus" class="form-label">Prime (DT) *</label>
            <input type="number" step="0.001" min="0"
                   name="bonus" id="bonus"
                   class="form-control" value="{{ old('bonus', '0') }}" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Statut *</label>
            <select name="status" id="status" class="form-select" required>
                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                    Actif
                </option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>
                    Inactif
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" id="notes" class="form-control"
                      rows="3">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>

        <a href="{{ route('drivers.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection

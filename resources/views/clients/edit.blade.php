@extends('layouts.app')

@section('title', 'Modifier un client - Sable & Gravier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold mb-1">Modifier un client</h1>
        <p class="text-muted mb-0">{{ $client->code }} — {{ $client->name }}</p>
    </div>

    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
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
        <form action="{{ route('clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Code client *</label>
                    <input type="text" name="code" class="form-control"
                           value="{{ old('code', $client->code) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nom complet / Nom du contact *</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $client->name) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nom de l'entreprise</label>
                    <input type="text" name="company_name" class="form-control"
                           value="{{ old('company_name', $client->company_name) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Type de client *</label>
                    <select name="client_type" class="form-select" required>
                        <option value="company" @selected(old('client_type', $client->client_type) === 'company')>Entreprise</option>
                        <option value="individual" @selected(old('client_type', $client->client_type) === 'individual')>Particulier</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="phone" class="form-control"
                           value="{{ old('phone', $client->phone) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Téléphone secondaire</label>
                    <input type="text" name="phone_secondary" class="form-control"
                           value="{{ old('phone_secondary', $client->phone_secondary) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email', $client->email) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Matricule fiscal</label>
                    <input type="text" name="tax_number" class="form-control"
                           value="{{ old('tax_number', $client->tax_number) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Gouvernorat</label>
                    <input type="text" name="gouvernorat" class="form-control"
                           value="{{ old('gouvernorat', $client->gouvernorat) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Délégation</label>
                    <input type="text" name="delegation" class="form-control"
                           value="{{ old('delegation', $client->delegation) }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="address" class="form-control"
                           value="{{ old('address', $client->address) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Plafond de crédit (DT)</label>
                    <input type="number" name="credit_limit" class="form-control"
                           value="{{ old('credit_limit', $client->credit_limit) }}"
                           min="0" step="0.001" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Statut *</label>
                    <select name="status" class="form-select" required>
                        <option value="active" @selected(old('status', $client->status) === 'active')>Actif</option>
                        <option value="inactive" @selected(old('status', $client->status) === 'inactive')>Inactif</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $client->notes) }}</textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection

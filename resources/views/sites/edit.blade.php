@extends('layouts.app')

@section('title', 'Modifier un site - Sable & Gravier')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Modifier un site</h1>

        <a href="{{ route('sites.index') }}" class="btn btn-secondary">
            Retour aux sites
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('sites.update', $site->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nom du site</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $site->name) }}"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">Type de site</label>
                    <select
                        id="type"
                        name="type"
                        class="form-select @error('type') is-invalid @enderror"
                        required
                    >
                        <option value="carriere"
                            {{ old('type', $site->type) === 'carriere' ? 'selected' : '' }}>
                            Carrière
                        </option>
                        <option value="depot"
                            {{ old('type', $site->type) === 'depot' ? 'selected' : '' }}>
                            Dépôt
                        </option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gouvernorat" class="form-label">Gouvernorat</label>
                        <input
                            type="text"
                            id="gouvernorat"
                            name="gouvernorat"
                            class="form-control @error('gouvernorat') is-invalid @enderror"
                            value="{{ old('gouvernorat', $site->gouvernorat) }}"
                            required
                        >
                        @error('gouvernorat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="delegation" class="form-label">Délégation</label>
                        <input
                            type="text"
                            id="delegation"
                            name="delegation"
                            class="form-control @error('delegation') is-invalid @enderror"
                            value="{{ old('delegation', $site->delegation) }}"
                            required
                        >
                        @error('delegation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Adresse</label>
                    <input
                        type="text"
                        id="address"
                        name="address"
                        class="form-control @error('address') is-invalid @enderror"
                        value="{{ old('address', $site->address) }}"
                    >
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Statut</label>
                    <select
                        id="status"
                        name="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required
                    >
                        <option value="active"
                            {{ old('status', $site->status) === 'active' ? 'selected' : '' }}>
                            Actif
                        </option>
                        <option value="inactive"
                            {{ old('status', $site->status) === 'inactive' ? 'selected' : '' }}>
                            Inactif
                        </option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tracks_stock" class="form-label">
                        Gestion du stock
                    </label>

                    <select
                        id="tracks_stock"
                        name="tracks_stock"
                        class="form-select @error('tracks_stock') is-invalid @enderror"
                        required
                    >
                        <option
                            value="1"
                            {{ (string) old('tracks_stock', (int) $site->tracks_stock) === '1' ? 'selected' : '' }}
                        >
                            Oui — ce site gère le stock
                        </option>

                        <option
                            value="0"
                            {{ (string) old('tracks_stock', (int) $site->tracks_stock) === '0' ? 'selected' : '' }}
                        >
                            Non — chargement externe, sans stock suivi
                        </option>
                    </select>

                    @error('tracks_stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div class="form-text">
                        Choisir Oui uniquement si les quantités de ce site
                        doivent être suivies dans le logiciel.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    Enregistrer les modifications
                </button>

                <a href="{{ route('sites.index') }}" class="btn btn-outline-secondary">
                    Annuler
                </a>
            </form>
        </div>
    </div>
</div>
@endsection

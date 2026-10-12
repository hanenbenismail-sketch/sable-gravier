<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un site - Sable & Gravier</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Ajouter un site</h1>
            <a href="{{ route('sites.index') }}" class="btn btn-secondary">
                Retour aux sites
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
                <form action="{{ route('sites.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du site</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control"
                            value="{{ old('name') }}"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type de site</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <option value="carriere" {{ old('type') === 'carriere' ? 'selected' : '' }}>
                                Carrière
                            </option>
                            <option value="depot" {{ old('type') === 'depot' ? 'selected' : '' }}>
                                Dépôt
                            </option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gouvernorat" class="form-label">Gouvernorat</label>
                            <input
                                type="text"
                                name="gouvernorat"
                                id="gouvernorat"
                                class="form-control"
                                value="{{ old('gouvernorat') }}"
                                required
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="delegation" class="form-label">Délégation</label>
                            <input
                                type="text"
                                name="delegation"
                                id="delegation"
                                class="form-control"
                                value="{{ old('delegation') }}"
                                required
                            >
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Adresse</label>
                        <textarea
                            name="address"
                            id="address"
                            class="form-control"
                            rows="3"
                        >{{ old('address') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Statut</label>
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
                        <label for="tracks_stock" class="form-label">
                            Gestion du stock
                        </label>
                        <select
                            name="tracks_stock"
                            id="tracks_stock"
                            class="form-select"
                            required
                        >
                            <option value="1" {{ old('tracks_stock', '1') === '1' ? 'selected' : '' }}>
                                Oui — ce site gère le stock
                            </option>
                            <option value="0" {{ old('tracks_stock') === '0' ? 'selected' : '' }}>
                                Non — chargement externe, sans stock suivi
                            </option>
                        </select>
                        <div class="form-text">
                            Choisir Oui uniquement si les quantités de ce site
                            doivent être suivies dans le logiciel.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Enregistrer
                    </button>

                    <a href="{{ route('sites.index') }}" class="btn btn-outline-secondary">
                        Annuler
                    </a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
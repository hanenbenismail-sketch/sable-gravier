<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un site</title>

```
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>
```

</head>

<body>
    <div class="container mt-5">

```
    <h1 class="mb-4">Ajouter un site</h1>

    <div class="card">
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
                    <label for="type" class="form-label">Type</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="">-- Choisir --</option>
                        <option value="carriere">Carrière</option>
                        <option value="depot">Dépôt</option>
                    </select>
                </div>

                <div class="mb-3">
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

                <div class="mb-3">
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
                        <option value="active">Actif</option>
                        <option value="inactive">Inactif</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Enregistrer
                </button>

                <a href="{{ route('sites.index') }}" class="btn btn-secondary">
                    Annuler
                </a>

            </form>

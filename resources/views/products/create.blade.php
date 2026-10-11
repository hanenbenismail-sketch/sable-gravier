<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>
    <div class="container mt-5">

        <h1 class="mb-4">Ajouter un produit</h1>

        <div class="card">
            <div class="card-body">

                <form action="{{ route('products.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du produit</label>
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
                        <label for="code" class="form-label">Code produit</label>
                        <input
                            type="text"
                            name="code"
                            id="code"
                            class="form-control"
                            value="{{ old('code') }}"
                            placeholder="Ex: GRV-001"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <option value="sable">Sable</option>
                            <option value="gravier">Gravier</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="unit" class="form-label">Unité</label>
                        <select name="unit" id="unit" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <option value="tonne">Tonne</option>
                            <option value="m3">m³</option>
                            <option value="kg">Kg</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="purchase_price" class="form-label">
                            Prix d'achat
                        </label>
                        <input
                            type="number"
                            name="purchase_price"
                            id="purchase_price"
                            class="form-control"
                            value="{{ old('purchase_price') }}"
                            min="0"
                            step="0.001"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="sale_price" class="form-label">
                            Prix de vente
                        </label>
                        <input
                            type="number"
                            name="sale_price"
                            id="sale_price"
                            class="form-control"
                            value="{{ old('sale_price') }}"
                            min="0"
                            step="0.001"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="min_stock" class="form-label">
                            Stock minimum
                        </label>
                        <input
                            type="number"
                            name="min_stock"
                            id="min_stock"
                            class="form-control"
                            value="{{ old('min_stock', 0) }}"
                            min="0"
                            step="0.001"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Statut</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="active">Actif</option>
                            <option value="inactive">Inactif</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Description
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            class="form-control"
                            rows="3"
                        >{{ old('description') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Enregistrer
                    </button>

                    <a
                        href="{{ route('products.index') }}"
                        class="btn btn-secondary"
                    >
                        Annuler
                    </a>

                </form>

            </div>
        </div>

    </div>
</body>
</html>

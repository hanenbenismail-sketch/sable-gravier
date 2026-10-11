<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>
    <div class="container mt-5">

        <h1 class="mb-4">Modifier un produit</h1>

        <div class="card">
            <div class="card-body">

                <form
                    action="{{ route('products.update', $product->id) }}"
                    method="POST"
                >
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Nom du produit
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control"
                            value="{{ old('name', $product->name) }}"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">
                            Code produit
                        </label>

                        <input
                            type="text"
                            name="code"
                            id="code"
                            class="form-control"
                            value="{{ old('code', $product->code) }}"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">
                            Type
                        </label>

                        <select
                            name="type"
                            id="type"
                            class="form-select"
                            required
                        >
                            <option
                                value="sable"
                                {{ old('type', $product->type) === 'sable' ? 'selected' : '' }}
                            >
                                Sable
                            </option>

                            <option
                                value="gravier"
                                {{ old('type', $product->type) === 'gravier' ? 'selected' : '' }}
                            >
                                Gravier
                            </option>

                            <option
                                value="autre"
                                {{ old('type', $product->type) === 'autre' ? 'selected' : '' }}
                            >
                                Autre
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="unit" class="form-label">
                            Unité
                        </label>

                        <select
                            name="unit"
                            id="unit"
                            class="form-select"
                            required
                        >
                            <option
                                value="tonne"
                                {{ old('unit', $product->unit) === 'tonne' ? 'selected' : '' }}
                            >
                                Tonne
                            </option>

                            <option
                                value="m3"
                                {{ old('unit', $product->unit) === 'm3' ? 'selected' : '' }}
                            >
                                m³
                            </option>

                            <option
                                value="kg"
                                {{ old('unit', $product->unit) === 'kg' ? 'selected' : '' }}
                            >
                                Kg
                            </option>
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
                            value="{{ old('purchase_price', $product->purchase_price) }}"
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
                            value="{{ old('sale_price', $product->sale_price) }}"
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
                            value="{{ old('min_stock', $product->min_stock) }}"
                            min="0"
                            step="0.001"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">
                            Statut
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-select"
                            required
                        >
                            <option
                                value="active"
                                {{ old('status', $product->status) === 'active' ? 'selected' : '' }}
                            >
                                Actif
                            </option>

                            <option
                                value="inactive"
                                {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}
                            >
                                Inactif
                            </option>
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
                        >{{ old('description', $product->description) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Enregistrer les modifications
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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sortie de stock</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1>Sortie de stock</h1>

        <a
            href="{{ route('stocks.index') }}"
            class="btn btn-secondary"
        >
            Retour
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

    <div class="card">

        <div class="card-body">

            <form action="{{ route('stocks.destroy') }}" method="POST">

                @csrf

                <div class="mb-3">

                    <label for="site_id" class="form-label">
                        Site
                    </label>

                    <select
                        name="site_id"
                        id="site_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            -- Sélectionner un site --
                        </option>

                        @foreach ($sites as $site)

                            <option
                                value="{{ $site->id }}"
                                {{ old('site_id') == $site->id ? 'selected' : '' }}
                            >
                                {{ $site->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="mb-3">

                    <label for="product_id" class="form-label">
                        Produit
                    </label>

                    <select
                        name="product_id"
                        id="product_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            -- Sélectionner un produit --
                        </option>

                        @foreach ($products as $product)

                            <option
                                value="{{ $product->id }}"
                                {{ old('product_id') == $product->id ? 'selected' : '' }}
                            >
                                {{ $product->name }} ({{ $product->code }})
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="mb-3">

                    <label for="quantity" class="form-label">
                        Quantité
                    </label>

                    <input
                        type="number"
                        name="quantity"
                        id="quantity"
                        class="form-control"
                        step="0.001"
                        min="0.001"
                        placeholder="Exemple : 15"
                        value="{{ old('quantity') }}"
                        required
                    >

                    <div class="form-text">
                        Quantité à retirer du stock.
                    </div>

                </div>

                <div class="mb-3">

                    <label for="note" class="form-label">
                        Note
                    </label>

                    <input
                        type="text"
                        name="note"
                        id="note"
                        class="form-control"
                        maxlength="255"
                        placeholder="Exemple : Sortie de sable"
                        value="{{ old('note') }}"
                    >

                </div>

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-danger"
                    >
                        Retirer du stock
                    </button>

                    <a
                        href="{{ route('stocks.index') }}"
                        class="btn btn-secondary"
                    >
                        Annuler
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>

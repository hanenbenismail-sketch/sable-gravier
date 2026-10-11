<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du produit</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Détails du produit</h1>

            <a
                href="{{ route('products.index') }}"
                class="btn btn-secondary"
            >
                Retour
            </a>
        </div>

        <div class="card">
            <div class="card-body">

                <h3 class="mb-4">{{ $product->name }}</h3>

                <table class="table table-bordered">

                    <tr>
                        <th width="30%">ID</th>
                        <td>{{ $product->id }}</td>
                    </tr>

                    <tr>
                        <th>Code</th>
                        <td>{{ $product->code }}</td>
                    </tr>

                    <tr>
                        <th>Type</th>
                        <td>{{ $product->type }}</td>
                    </tr>

                    <tr>
                        <th>Unité</th>
                        <td>{{ $product->unit }}</td>
                    </tr>

                    <tr>
                        <th>Prix d'achat</th>
                        <td>
                            {{ number_format($product->purchase_price, 3) }} DT
                        </td>
                    </tr>

                    <tr>
                        <th>Prix de vente</th>
                        <td>
                            {{ number_format($product->sale_price, 3) }} DT
                        </td>
                    </tr>

                    <tr>
                        <th>Stock minimum</th>
                        <td>{{ $product->min_stock }}</td>
                    </tr>

                    <tr>
                        <th>Statut</th>
                        <td>
                            @if ($product->status === 'active')
                                <span class="badge bg-success">
                                    Actif
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    Inactif
                                </span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>Description</th>
                        <td>
                            {{ $product->description ?: 'Aucune description.' }}
                        </td>
                    </tr>

                </table>

                <div class="mt-3">

                    <a
                        href="{{ route('products.edit', $product->id) }}"
                        class="btn btn-warning"
                    >
                        Modifier
                    </a>

                </div>

            </div>
        </div>

    </div>
</body>
</html>

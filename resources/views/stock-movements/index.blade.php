@extends('layouts.app')

@section('title', 'Historique des mouvements - Sable & Gravier')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="fw-bold mb-1">Historique des mouvements</h1>
            <p class="text-muted mb-0">Suivi des entrées et sorties de stock</p>
        </div>

        <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
            Retour au stock
        </a>
    </div>

    {{-- Filtres --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <strong>Filtres de recherche</strong>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('stock-movements.index') }}">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="site_id" class="form-label">Site</label>
                        <select name="site_id" id="site_id" class="form-select">
                            <option value="">Tous les sites</option>
                            @foreach ($sites as $site)
                                <option
                                    value="{{ $site->id }}"
                                    {{ request('site_id') == $site->id ? 'selected' : '' }}
                                >
                                    {{ $site->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="product_id" class="form-label">Produit</label>
                        <select name="product_id" id="product_id" class="form-select">
                            <option value="">Tous les produits</option>
                            @foreach ($products as $product)
                                <option
                                    value="{{ $product->id }}"
                                    {{ request('product_id') == $product->id ? 'selected' : '' }}
                                >
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="type" class="form-label">Type de mouvement</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">Tous les types</option>
                            <option value="IN" {{ request('type') === 'IN' ? 'selected' : '' }}>
                                Entrée (IN)
                            </option>
                            <option value="OUT" {{ request('type') === 'OUT' ? 'selected' : '' }}>
                                Sortie (OUT)
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="date_from" class="form-label">Date de début</label>
                        <input
                            type="date"
                            name="date_from"
                            id="date_from"
                            class="form-control"
                            value="{{ request('date_from') }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="date_to" class="form-label">Date de fin</label>
                        <input
                            type="date"
                            name="date_to"
                            id="date_to"
                            class="form-control"
                            value="{{ request('date_to') }}"
                        >
                    </div>

                    <div class="col-md-4 d-flex align-items-end flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">
                            Rechercher
                        </button>

                        <a
                            href="{{ route('stock-movements.index') }}"
                            class="btn btn-outline-secondary"
                        >
                            Réinitialiser
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Historique --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <strong>Résultats : {{ $movements->total() }} mouvement(s)</strong>
        </div>

        <div class="card-body">

            @if ($movements->count() > 0)

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">

                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Site</th>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Origine / Destination</th>
                                <th>Utilisateur</th>
                                <th>Note</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($movements as $movement)
                                <tr>
                                    <td>{{ $movement->id }}</td>

                                    <td>
                                        {{ $movement->movement_date?->format('d/m/Y H:i') ?? '-' }}
                                    </td>

                                    <td>
                                        @if ($movement->type === 'IN')
                                            <span class="badge bg-success">Entrée (IN)</span>
                                        @elseif ($movement->type === 'OUT')
                                            <span class="badge bg-danger">Sortie (OUT)</span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ $movement->type }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>{{ $movement->stock?->site?->name ?? '-' }}</td>

                                    <td>{{ $movement->stock?->product?->name ?? '-' }}</td>

                                    <td>
                                        {{ number_format((float) $movement->quantity, 3) }}
                                        {{ $movement->stock?->product?->unit ?? '' }}
                                    </td>

                                    <td>
                                        {{ $movement->fromSite?->name ?? '-' }}
                                        →
                                        {{ $movement->toSite?->name ?? '-' }}
                                    </td>

                                    <td>{{ $movement->user?->name ?? '-' }}</td>

                                    <td>{{ $movement->note ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                    <p class="text-muted mb-0">
                        Affichage de {{ $movements->firstItem() }}
                        à {{ $movements->lastItem() }}
                        sur {{ $movements->total() }} mouvements
                    </p>

                    <div>
                        {{ $movements->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            @else

                <div class="alert alert-info mb-0">
                    Aucun mouvement ne correspond aux filtres sélectionnés.
                </div>

            @endif

        </div>
    </div>

@endsection

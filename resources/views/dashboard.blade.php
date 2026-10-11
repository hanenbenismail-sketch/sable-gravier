@extends('layouts.app')

@section('title', 'Tableau de bord - Sable & Gravier')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">Tableau de bord</h1>
            <p class="text-muted mb-0">Gestion Sable &amp; Gravier</p>
        </div>

        <a href="{{ route('stocks.index') }}" class="btn btn-primary mt-2">
            Gestion du stock
        </a>
    </div>

    {{-- Indicateurs --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card border-primary h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Sites / Carrières</h6>
                    <h2 class="fw-bold">{{ $totalSites }}</h2>
                    <a href="{{ route('sites.index') }}" class="btn btn-outline-primary btn-sm">
                        Voir les sites
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-success h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Produits</h6>
                    <h2 class="fw-bold">{{ $totalProducts }}</h2>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-success btn-sm">
                        Voir les produits
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-dark h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Mouvements de stock</h6>
                    <h2 class="fw-bold">{{ $totalMovements }}</h2>
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-dark btn-sm">
                        Voir l'historique
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-success h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Entrées de stock</h6>
                    <h2 class="fw-bold">{{ $totalEntries }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-danger h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Sorties de stock</h6>
                    <h2 class="fw-bold">{{ $totalExits }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-warning h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Stocks à surveiller</h6>
                    <h2 class="fw-bold">{{ $lowStockCount }}</h2>
                    <a href="{{ route('stocks.index') }}" class="btn btn-outline-warning btn-sm">
                        Vérifier le stock
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- Quantité totale --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="text-muted">Quantité totale en stock</h5>
            <h2 class="fw-bold">{{ number_format((float) $totalStock, 3) }}</h2>
            <a href="{{ route('stocks.index') }}" class="btn btn-primary">
                Accéder au stock
            </a>
        </div>
    </div>

    {{-- Derniers mouvements --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <strong>5 derniers mouvements de stock</strong>
        </div>

        <div class="card-body">

            @if ($recentMovements->count() > 0)

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">

                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Site</th>
                                <th>Produit</th>
                                <th>Quantité</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($recentMovements as $movement)
                                <tr>
                                    <td>
                                        {{ $movement->movement_date?->format('d/m/Y H:i') ?? '-' }}
                                    </td>

                                    <td>
                                        @if ($movement->type === 'IN')
                                            <span class="badge bg-success">Entrée</span>
                                        @elseif ($movement->type === 'OUT')
                                            <span class="badge bg-danger">Sortie</span>
                                        @else
                                            {{ $movement->type }}
                                        @endif
                                    </td>

                                    <td>
                                        {{ $movement->stock?->site?->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $movement->stock?->product?->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ number_format((float) $movement->quantity, 3) }}
                                        {{ $movement->stock?->product?->unit ?? '' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="text-end mt-3">
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-dark btn-sm">
                        Consulter tous les mouvements
                    </a>
                </div>

            @else

                <div class="alert alert-info mb-0">
                    Aucun mouvement de stock enregistré.
                </div>

            @endif

        </div>
    </div>

@endsection

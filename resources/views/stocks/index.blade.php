@extends('layouts.app')

@section('title', 'Gestion du stock - Sable & Gravier')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="fw-bold mb-1">Gestion du stock</h1>
            <p class="text-muted mb-0">Suivi des quantités par site et par produit</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('stocks.create') }}" class="btn btn-success">
                + Entrée Stock
            </a>

            <a href="{{ route('stocks.remove') }}" class="btn btn-danger">
                - Sortie Stock
            </a>

            <a href="{{ route('stock-movements.index') }}" class="btn btn-primary">
                Historique des mouvements
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    @if ($stocks->count() > 0)

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">

                    <table class="table table-bordered table-striped align-middle mb-0">

                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Site</th>
                                <th>Produit</th>
                                <th>Code</th>
                                <th>Quantité</th>
                                <th>Réservée</th>
                                <th>Disponible</th>
                                <th>Dernière mise à jour</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($stocks as $stock)
                                <tr>
                                    <td>{{ $stock->id }}</td>

                                    <td>{{ $stock->site?->name ?? '-' }}</td>

                                    <td>{{ $stock->product?->name ?? '-' }}</td>

                                    <td>{{ $stock->product?->code ?? '-' }}</td>

                                    <td>
                                        {{ number_format((float) $stock->quantity, 3) }}
                                        {{ $stock->product?->unit ?? '' }}
                                    </td>

                                    <td>
                                        {{ number_format((float) $stock->reserved_quantity, 3) }}
                                        {{ $stock->product?->unit ?? '' }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ number_format((float) $stock->available_quantity, 3) }}
                                            {{ $stock->product?->unit ?? '' }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $stock->last_updated?->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>
            </div>
        </div>

    @else

        <div class="alert alert-info">
            Aucun stock enregistré pour le moment.
            Pour commencer, utilisez le bouton « Entrée Stock ».
        </div>

    @endif

@endsection

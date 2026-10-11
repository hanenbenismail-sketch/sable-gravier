@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Détails de la mission</h1>

        <div class="d-flex gap-2">
            <a href="{{ route('missions.edit', $mission) }}" class="btn btn-warning">
                Modifier
            </a>

            <a href="{{ route('missions.index') }}" class="btn btn-secondary">
                Retour
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Informations générales</div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <strong>Référence</strong>
                    <p>{{ $mission->reference }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Date</strong>
                    <p>{{ $mission->mission_date?->format('d/m/Y') ?? '—' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Type</strong>
                    <p>
                        @switch($mission->mission_type)
                            @case('delivery') Livraison @break
                            @case('transfer') Transfert @break
                            @case('return') Retour @break
                            @case('mixed') Mixte @break
                            @default {{ $mission->mission_type }}
                        @endswitch
                    </p>
                </div>

                <div class="col-md-4">
                    <strong>Statut</strong>
                    <p>
                        @switch($mission->status)
                            @case('planned')
                                <span class="badge bg-secondary">Planifiée</span>
                                @break
                            @case('in_progress')
                                <span class="badge bg-primary">En cours</span>
                                @break
                            @case('completed')
                                <span class="badge bg-success">Terminée</span>
                                @break
                            @case('cancelled')
                                <span class="badge bg-danger">Annulée</span>
                                @break
                        @endswitch
                    </p>
                </div>

                <div class="col-md-4">
                    <strong>Heure de départ</strong>
                    <p>{{ $mission->departure_time?->format('d/m/Y H:i') ?? '—' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Heure de retour</strong>
                    <p>{{ $mission->return_time?->format('d/m/Y H:i') ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Ressources affectées</div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <strong>Chauffeur</strong>
                    <p>{{ $mission->driver?->full_name ?? '—' }}</p>
                    <p>{{ $mission->driver?->phone ?? '' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Camion</strong>
                    <p>{{ $mission->truck?->registration_number ?? '—' }}</p>
                    <p>
                        {{ $mission->truck?->brand ?? '' }}
                        {{ $mission->truck?->model ?? '' }}
                    </p>
                </div>

                <div class="col-md-4">
                    <strong>Site de départ</strong>
                    <p>{{ $mission->departureSite?->name ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Distance, quantité et résultats financiers</div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <strong>Distance totale</strong>
                    <p>{{ number_format((float) $mission->total_distance, 3, ',', ' ') }} km</p>
                </div>

                <div class="col-md-3">
                    <strong>Quantité totale</strong>
                    <p>{{ number_format((float) $mission->total_quantity, 3, ',', ' ') }}</p>
                </div>

                <div class="col-md-3">
                    <strong>Revenu total</strong>
                    <p>{{ number_format((float) $mission->total_revenue, 3, ',', ' ') }} DT</p>
                </div>

                <div class="col-md-3">
                    <strong>Coût total</strong>
                    <p>{{ number_format((float) $mission->total_cost, 3, ',', ' ') }} DT</p>
                </div>

                <div class="col-md-3">
                    <strong>Bénéfice / perte</strong>
                    <p class="{{ (float) $mission->profit >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format((float) $mission->profit, 3, ',', ' ') }} DT
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Détail des coûts</div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <strong>Transport</strong>
                    <p>{{ number_format((float) $mission->transport_cost, 3, ',', ' ') }} DT</p>
                </div>

                <div class="col-md-3">
                    <strong>Carburant</strong>
                    <p>{{ number_format((float) $mission->fuel_cost, 3, ',', ' ') }} DT</p>
                </div>

                <div class="col-md-3">
                    <strong>Chauffeur</strong>
                    <p>{{ number_format((float) $mission->driver_cost, 3, ',', ' ') }} DT</p>
                </div>

                <div class="col-md-3">
                    <strong>Autres coûts</strong>
                    <p>{{ number_format((float) $mission->other_cost, 3, ',', ' ') }} DT</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Arrêts de la mission</div>

        <div class="card-body">
            @if ($mission->stops->isEmpty())
                <p class="text-muted mb-0">
                    Aucun arrêt enregistré pour cette mission.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Ordre</th>
                                <th>Type</th>
                                <th>Site</th>
                                <th>Client</th>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Chargement</th>
                                <th>Livraison</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($mission->stops->sortBy('stop_order') as $stop)
                                <tr>
                                    <td>{{ $stop->stop_order }}</td>
                                    <td>{{ $stop->stop_type }}</td>
                                    <td>{{ $stop->site?->name ?? '—' }}</td>
                                    <td>{{ $stop->client?->name ?? '—' }}</td>
                                    <td>{{ $stop->product?->name ?? '—' }}</td>
                                    <td>
                                        {{ number_format((float) $stop->quantity, 3, ',', ' ') }}
                                        {{ $stop->unit }}
                                    </td>
                                    <td>{{ $stop->loading_status ?? '—' }}</td>
                                    <td>{{ $stop->delivery_status ?? '—' }}</td>

                                    <td>
                                        @if (
                                            $stop->stop_type === 'loading'
                                            && $stop->loading_status === 'pending'
                                            && in_array($mission->status, ['planned', 'in_progress'], true)
                                        )
                                            <form
                                                action="{{ route('missions.stops.load', ['mission' => $mission->id, 'stop' => $stop->id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Confirmer le chargement et la sortie du stock ?');"
                                            >
                                                @csrf

                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    Charger
                                                </button>
                                            </form>
                                        @elseif ($stop->loading_status === 'loaded')
                                            <span class="badge bg-success">Chargé</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Notes</div>

        <div class="card-body">
            {!! nl2br(e($mission->notes ?: 'Aucune note.')) !!}
        </div>
    </div>

</div>
@endsection

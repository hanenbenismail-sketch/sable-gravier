@extends('layouts.app')

@section('content')
<div class="container mt-4">

    @php
        $activeMission = in_array($mission->status, ['planned', 'in_progress'], true);

        $loadedStops = $mission->stops
            ->where('stop_type', 'loading')
            ->where('loading_status', 'loaded');

        $loadedQuantity = (float) $loadedStops->sum('quantity');

        $deliveredQuantity = (float) $mission->stops
            ->whereIn('stop_type', ['delivery', 'unloading'])
            ->sum('delivered_quantity');

        $returnedQuantity = (float) ($mission->returned_quantity ?? 0);

        $remainingQuantity = max(
            round($loadedQuantity - $deliveredQuantity - $returnedQuantity, 3),
            0
        );

        $hasReturn = $returnedQuantity > 0;
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h1>Détails de la mission</h1>

        <div class="d-flex gap-2 flex-wrap">
            @if ($mission->status === 'planned')
                <form
                    action="{{ route('missions.start', $mission) }}"
                    method="POST"
                    onsubmit="return confirm('Confirmer le démarrage de cette mission ?');"
                >
                    @csrf
                    <button type="submit" class="btn btn-success">
                        Démarrer la mission
                    </button>
                </form>
            @endif

            <a href="{{ route('missions.edit', $mission) }}" class="btn btn-warning">
                Modifier
            </a>

            <a href="{{ route('missions.index') }}" class="btn btn-secondary">
                Retour
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Informations générales --}}
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
                            @default
                                <span class="badge bg-secondary">{{ $mission->status }}</span>
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

    {{-- Ressources affectées --}}
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
                    <p>{{ $mission->truck?->brand ?? '' }} {{ $mission->truck?->model ?? '' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Site de départ</strong>
                    <p>{{ $mission->departureSite?->name ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Résultats financiers --}}
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

    {{-- Détail des coûts --}}
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

    {{-- Arrêts de la mission --}}
    <div class="card mb-4">
        <div class="card-header">Arrêts de la mission</div>
        <div class="card-body">
            @if ($mission->stops->isEmpty())
                <p class="text-muted mb-0">Aucun arrêt enregistré pour cette mission.</p>
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
                                @php
                                    $plannedQuantity = (float) $stop->quantity;
                                    $stopDelivered = (float) ($stop->delivered_quantity ?? 0);
                                    $stopRemaining = max(
                                        round($plannedQuantity - $stopDelivered, 3),
                                        0
                                    );

                                    $isDeliveryStop = in_array(
                                        $stop->stop_type,
                                        ['delivery', 'unloading'],
                                        true
                                    );
                                @endphp

                                <tr>
                                    <td>{{ $stop->stop_order }}</td>
                                    <td>{{ $stop->stop_type }}</td>
                                    <td>{{ $stop->site?->name ?? '—' }}</td>
                                    <td>{{ $stop->client?->name ?? '—' }}</td>
                                    <td>{{ $stop->product?->name ?? '—' }}</td>

                                    <td>
                                        @if (
                                            $stop->stop_type === 'loading'
                                            && $stop->loading_status === 'pending'
                                        )
                                            <span class="text-muted">
                                                À confirmer au chargement
                                            </span>
                                        @else
                                            {{ number_format($plannedQuantity, 3, ',', ' ') }}
                                            {{ $stop->unit }}
                                        @endif
                                    </td>

                                    <td>
                                        @if ($stop->loading_status === 'loaded')
                                            <span class="badge bg-success">Chargé</span>
                                        @elseif ($stop->loading_status === 'pending')
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($isDeliveryStop)
                                            @if ($stop->delivery_status === 'delivered')
                                                <span class="badge bg-success">Livrée</span>
                                            @elseif ($stop->delivery_status === 'partial')
                                                <span class="badge bg-warning text-dark">Partielle</span>
                                            @elseif ($stop->delivery_status === 'pending')
                                                <span class="badge bg-secondary">En attente</span>
                                            @else
                                                <span class="text-muted">{{ $stop->delivery_status ?? '—' }}</span>
                                            @endif

                                            <small class="d-block mt-1">
                                                Livrée :
                                                {{ number_format($stopDelivered, 3, ',', ' ') }}
                                                /
                                                {{ number_format($plannedQuantity, 3, ',', ' ') }}
                                                {{ $stop->unit }}
                                            </small>

                                            @if ($stopRemaining > 0)
                                                <small class="d-block text-muted">
                                                    Reste :
                                                    {{ number_format($stopRemaining, 3, ',', ' ') }}
                                                    {{ $stop->unit }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if (
                                            $stop->stop_type === 'loading'
                                            && $stop->loading_status === 'pending'
                                            && $activeMission
                                        )
                                            <form
                                                action="{{ route('missions.stops.load', [
                                                    'mission' => $mission->id,
                                                    'stop' => $stop->id
                                                ]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Confirmer le chargement et la sortie du stock ?');"
                                            >
                                                @csrf

                                                <label
                                                    for="quantity_{{ $stop->id }}"
                                                    class="form-label small mb-1"
                                                >
                                                    Quantité chargée ({{ $stop->unit }})
                                                </label>

                                                <input
                                                    type="number"
                                                    id="quantity_{{ $stop->id }}"
                                                    name="quantity"
                                                    class="form-control form-control-sm mb-2"
                                                    min="0.001"
                                                    max="999999999"
                                                    step="0.001"
                                                    value="{{ old('quantity') }}"
                                                    required
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    Confirmer le chargement
                                                </button>
                                            </form>

                                        @elseif (
                                            $isDeliveryStop
                                            && in_array($stop->delivery_status, ['pending', 'partial'], true)
                                            && $activeMission
                                            && $stopRemaining > 0
                                        )
                                            <form
                                                action="{{ route('missions.stops.deliver', [
                                                    'mission' => $mission->id,
                                                    'stop' => $stop->id
                                                ]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Confirmer la quantité livrée ?');"
                                            >
                                                @csrf

                                                <label
                                                    for="delivered_quantity_{{ $stop->id }}"
                                                    class="form-label small mb-1"
                                                >
                                                    Quantité à livrer
                                                </label>

                                                <input
                                                    type="number"
                                                    id="delivered_quantity_{{ $stop->id }}"
                                                    name="delivered_quantity"
                                                    class="form-control form-control-sm mb-2"
                                                    min="0.001"
                                                    max="{{ number_format($stopRemaining, 3, '.', '') }}"
                                                    step="0.001"
                                                    value="{{ old('delivered_quantity') }}"
                                                    required
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-success"
                                                >
                                                    Livrer
                                                </button>
                                            </form>

                                        @elseif ($isDeliveryStop && $stopRemaining <= 0)
                                            <span class="badge bg-success">Livraison terminée</span>
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

    {{-- Suivi du stock transporté --}}
    <div class="card mb-4">
        <div class="card-header">Suivi de la marchandise</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <strong>Quantité chargée</strong>
                    <p>{{ number_format($loadedQuantity, 3, ',', ' ') }}</p>
                </div>

                <div class="col-md-3">
                    <strong>Quantité livrée</strong>
                    <p>{{ number_format($deliveredQuantity, 3, ',', ' ') }}</p>
                </div>

                <div class="col-md-3">
                    <strong>Quantité retournée</strong>
                    <p>{{ number_format($returnedQuantity, 3, ',', ' ') }}</p>
                </div>

                <div class="col-md-3">
                    <strong>Quantité restante</strong>
                    <p>{{ number_format($remainingQuantity, 3, ',', ' ') }}</p>
                </div>
            </div>

            @if ($hasReturn)
                <hr>
                <p class="mb-1">
                    <strong>Site de retour :</strong>
                    {{ $mission->returnSite?->name ?? 'Site non disponible' }}
                </p>
                <span class="badge bg-success">Retour enregistré</span>
            @endif
        </div>
    </div>

    {{-- Retour de marchandise --}}
    @if ($activeMission && $loadedStops->isNotEmpty() && !$hasReturn && $remainingQuantity > 0)
        <div class="card mb-4 border-warning">
            <div class="card-header">Retour de marchandise au stock</div>
            <div class="card-body">
                <p>
                    Quantité restante à retourner :
                    <strong>{{ number_format($remainingQuantity, 3, ',', ' ') }}</strong>
                </p>

                <div class="alert alert-info">
                    La quantité retournée doit correspondre à toute la quantité restante
                    après les livraisons enregistrées.
                </div>

                <form
                    action="{{ route('missions.return', $mission) }}"
                    method="POST"
                    onsubmit="return confirm('Confirmer le retour de la marchandise au stock ?');"
                >
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="return_site_id" class="form-label">
                                Site de retour
                            </label>

                            <select
                                name="return_site_id"
                                id="return_site_id"
                                class="form-select"
                                required
                            >
                                <option value="">Choisir un site</option>

                                @foreach ($sites as $site)
                                    <option
                                        value="{{ $site->id }}"
                                        @selected(old('return_site_id') == $site->id)
                                    >
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="returned_quantity" class="form-label">
                                Quantité retournée
                            </label>

                            <input
                                type="number"
                                name="returned_quantity"
                                id="returned_quantity"
                                class="form-control"
                                min="0.001"
                                max="{{ number_format($remainingQuantity, 3, '.', '') }}"
                                step="0.001"
                                value="{{ old(
                                    'returned_quantity',
                                    number_format($remainingQuantity, 3, '.', '')
                                ) }}"
                                required
                            >
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-warning">
                                Enregistrer le retour au stock
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Clôture de mission --}}
    @if ($activeMission)
        <div class="card mb-4">
            <div class="card-header">Clôture de la mission</div>
            <div class="card-body">
                @if ($remainingQuantity > 0)
                    <p class="text-muted">
                        La mission ne pourra être terminée qu'après la livraison
                        ou le retour au stock de la quantité restante.
                    </p>
                @else
                    <p class="text-success">
                        Aucune quantité restante selon les quantités enregistrées.
                    </p>
                @endif

                <form
                    action="{{ route('missions.complete', $mission) }}"
                    method="POST"
                    onsubmit="return confirm('Confirmer la clôture de cette mission ?');"
                >
                    @csrf

                    <button type="submit" class="btn btn-dark">
                        Terminer la mission
                    </button>
                </form>

                <small class="text-muted d-block mt-2">
                    Le serveur vérifie les quantités avant de clôturer la mission.
                </small>
            </div>
        </div>
    @endif

    {{-- Notes --}}
    <div class="card mb-4">
        <div class="card-header">Notes</div>
        <div class="card-body">
            {!! nl2br(e($mission->notes ?: 'Aucune note.')) !!}
        </div>
    </div>

</div>
@endsection

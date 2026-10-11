@extends('layouts.app')

@section('title', 'Missions')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Missions</h1>
        <a href="{{ route('missions.create') }}" class="btn btn-primary">
            + Nouvelle mission
        </a>
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

    <div class="card shadow-sm">
        <div class="card-body">

            @if ($missions->isEmpty())
                <p class="text-muted mb-0">
                    Aucune mission enregistrée pour le moment.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Référence</th>
                                <th>Date</th>
                                <th>Chauffeur</th>
                                <th>Camion</th>
                                <th>Site de départ</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th class="text-end">Coût total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($missions as $mission)
                                <tr>
                                    <td class="fw-semibold">
                                        {{ $mission->reference }}
                                    </td>

                                    <td>
                                        {{ $mission->mission_date?->format('d/m/Y') ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $mission->driver?->full_name ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $mission->truck?->registration_number ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $mission->departureSite?->name ?? '—' }}
                                    </td>

                                    <td>
                                        @switch($mission->mission_type)
                                            @case('delivery')
                                                Livraison
                                                @break
                                            @case('transfer')
                                                Transfert
                                                @break
                                            @case('return')
                                                Retour
                                                @break
                                            @case('mixed')
                                                Mixte
                                                @break
                                            @default
                                                {{ ucfirst($mission->mission_type) }}
                                        @endswitch
                                    </td>

                                    <td>
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
                                                <span class="badge bg-light text-dark">
                                                    {{ ucfirst($mission->status) }}
                                                </span>
                                        @endswitch
                                    </td>

                                    <td class="text-end text-nowrap">
                                        {{ number_format((float) $mission->total_cost, 3, ',', ' ') }} DT
                                    </td>

                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a
                                                href="{{ route('missions.show', $mission) }}"
                                                class="btn btn-sm btn-info"
                                            >
                                                Voir
                                            </a>

                                            <a
                                                href="{{ route('missions.edit', $mission) }}"
                                                class="btn btn-sm btn-warning"
                                            >
                                                Modifier
                                            </a>

                                            <form
                                                action="{{ route('missions.destroy', $mission) }}"
                                                method="POST"
                                                onsubmit="return confirm('Supprimer cette mission ?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $missions->links() }}
            @endif

        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Gestion des camions - Sable & Gravier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold mb-1">Gestion des camions</h1>
        <p class="text-muted mb-0">Liste des camions de transport</p>
    </div>

    <a href="{{ route('trucks.create') }}" class="btn btn-success">
        + Ajouter un camion
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if ($trucks->count())
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Immatriculation</th>
                        <th>Marque</th>
                        <th>Modèle</th>
                        <th>Capacité</th>
                        <th>Kilométrage</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($trucks as $truck)
                        <tr>
                            <td>{{ $truck->registration_number }}</td>
                            <td>{{ $truck->brand ?? '-' }}</td>
                            <td>{{ $truck->model ?? '-' }}</td>
                            <td>{{ $truck->capacity }} {{ $truck->unit }}</td>
                            <td>{{ number_format($truck->current_kilometer, 0, ',', ' ') }} km</td>
                            <td>
                                @php
                                    $statusLabels = [
                                        'AVAILABLE' => 'Disponible',
                                        'IN_MISSION' => 'En mission',
                                        'MAINTENANCE' => 'En maintenance',
                                        'INACTIVE' => 'Inactif',
                                    ];

                                    $statusClasses = [
                                        'AVAILABLE' => 'bg-success',
                                        'IN_MISSION' => 'bg-primary',
                                        'MAINTENANCE' => 'bg-warning text-dark',
                                        'INACTIVE' => 'bg-secondary',
                                    ];
                                @endphp

                                <span class="badge {{ $statusClasses[$truck->status] ?? 'bg-secondary' }}">
                                    {{ $statusLabels[$truck->status] ?? $truck->status }}
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('trucks.edit', $truck) }}"
                                   class="btn btn-sm btn-primary">
                                    Modifier
                                </a>

                                <form action="{{ route('trucks.destroy', $truck) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Supprimer ce camion ?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $trucks->links() }}
        </div>
    </div>
@else
    <div class="alert alert-info">
        Aucun camion enregistré. Cliquez sur « Ajouter un camion » pour commencer.
    </div>
@endif
@endsection

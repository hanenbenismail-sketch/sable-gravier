@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Gestion des chauffeurs</h1>
            <p>Liste des chauffeurs de transport</p>
        </div>

        <a href="{{ route('drivers.create') }}" class="btn btn-primary">
            Ajouter un chauffeur
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($drivers->count())
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nom et prénom</th>
                        <th>Téléphone</th>
                        <th>N° permis</th>
                        <th>Type de salaire</th>
                        <th>Salaire de base</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($drivers as $driver)
                        <tr>
                            <td>{{ $driver->full_name }}</td>
                            <td>{{ $driver->phone ?? '—' }}</td>
                            <td>{{ $driver->license_number }}</td>

                            <td>
                                @switch($driver->salary_type)
                                    @case('monthly')
                                        Mensuel
                                        @break
                                    @case('daily')
                                        Journalier
                                        @break
                                    @case('per_trip')
                                        Par trajet
                                        @break
                                    @case('per_km')
                                        Par kilomètre
                                        @break
                                    @default
                                        —
                                @endswitch
                            </td>

                            <td>{{ number_format((float) $driver->base_salary, 3, ',', ' ') }} DT</td>

                            <td>
                                @if($driver->status === 'active')
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('drivers.show', $driver) }}"
                                   class="btn btn-sm btn-info">
                                    Voir
                                </a>

                                <a href="{{ route('drivers.edit', $driver) }}"
                                   class="btn btn-sm btn-warning">
                                    Modifier
                                </a>

                                <form action="{{ route('drivers.destroy', $driver) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Supprimer ce chauffeur ?')">
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

            {{ $drivers->links() }}
        </div>
    @else
        <div class="alert alert-info">
            Aucun chauffeur enregistré. Cliquez sur « Ajouter un chauffeur » pour commencer.
        </div>
    @endif
</div>
@endsection

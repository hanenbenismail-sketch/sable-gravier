@extends('layouts.app')

@section('title', 'Gestion des clients - Sable & Gravier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold mb-1">Gestion des clients</h1>
        <p class="text-muted mb-0">Liste des clients</p>
    </div>

    <a href="{{ route('clients.create') }}" class="btn btn-success">
        + Ajouter un client
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($clients->count())
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Code</th>
                        <th>Nom</th>
                        <th>Société</th>
                        <th>Téléphone</th>
                        <th>Gouvernorat</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clients as $client)
                        <tr>
                            <td>{{ $client->code }}</td>
                            <td>{{ $client->name }}</td>
                            <td>{{ $client->company_name ?? '-' }}</td>
                            <td>{{ $client->phone ?? '-' }}</td>
                            <td>{{ $client->gouvernorat ?? '-' }}</td>
                            <td>
                                {{ $client->client_type === 'company' ? 'Entreprise' : 'Particulier' }}
                            </td>
                            <td>
                                <span class="badge {{ $client->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $client->status === 'active' ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('clients.edit', $client) }}"
                                   class="btn btn-sm btn-primary">Modifier</a>

                                <form action="{{ route('clients.destroy', $client) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Supprimer ce client ?')">
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

            {{ $clients->links() }}
        </div>
    </div>
@else
    <div class="alert alert-info">
        Aucun client enregistré. Cliquez sur « Ajouter un client » pour commencer.
    </div>
@endif
@endsection

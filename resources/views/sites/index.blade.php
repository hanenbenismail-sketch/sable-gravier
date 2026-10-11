@extends('layouts.app')

@section('title', 'Sites - Sable & Gravier')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">Liste des sites</h1>
            <p class="text-muted mb-0">Gestion des sites et carrières</p>
        </div>

        <a href="{{ route('sites.create') }}" class="btn btn-primary mt-2">
            + Ajouter un site
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    @if ($sites->count() > 0)

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">

                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Gouvernorat</th>
                                <th>Délégation</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($sites as $site)
                                <tr>
                                    <td>{{ $site->id }}</td>
                                    <td>{{ $site->name }}</td>
                                    <td>{{ $site->type }}</td>
                                    <td>{{ $site->gouvernorat }}</td>
                                    <td>{{ $site->delegation }}</td>

                                    <td>
                                        @if ($site->status === 'active')
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex flex-wrap gap-2">

                                            <a
                                                href="{{ route('sites.edit', $site->id) }}"
                                                class="btn btn-sm btn-warning"
                                            >
                                                Modifier
                                            </a>

                                            <form
                                                action="{{ route('sites.destroy', $site->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Voulez-vous vraiment supprimer ce site ?');"
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
            </div>
        </div>

    @else

        <div class="alert alert-info">
            Aucun site enregistré.
            <a href="{{ route('sites.create') }}" class="alert-link">
                Ajouter le premier site
            </a>
        </div>

    @endif

@endsection

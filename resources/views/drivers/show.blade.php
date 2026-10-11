@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Détails du chauffeur</h1>
            <p>Informations personnelles et rémunération.</p>
        </div>

        <div>
            <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-warning">
                Modifier
            </a>

            <a href="{{ route('drivers.index') }}" class="btn btn-secondary">
                Retour à la liste
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Informations personnelles
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Prénom :</strong>
                    <div>{{ $driver->first_name }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Nom :</strong>
                    <div>{{ $driver->last_name }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Nom complet :</strong>
                    <div>{{ $driver->full_name }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Téléphone :</strong>
                    <div>{{ $driver->phone ?: 'Non renseigné' }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Numéro de permis :</strong>
                    <div>{{ $driver->license_number }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Catégorie du permis :</strong>
                    <div>{{ $driver->license_category ?: 'Non renseignée' }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Statut :</strong>
                    <div>
                        @if ($driver->status === 'active')
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-secondary">Inactif</span>
                        @endif
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <strong>Notes :</strong>
                    <div>{{ $driver->notes ?: 'Aucune note' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Informations sur le salaire
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Type de salaire :</strong>
                    <div>
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
                                Non défini
                        @endswitch
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Salaire de base :</strong>
                    <div>{{ number_format((float) $driver->base_salary, 3, ',', ' ') }} DT</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Salaire par jour :</strong>
                    <div>{{ number_format((float) $driver->salary_per_day, 3, ',', ' ') }} DT</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Salaire par trajet :</strong>
                    <div>{{ number_format((float) $driver->salary_per_trip, 3, ',', ' ') }} DT</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Salaire par kilomètre :</strong>
                    <div>{{ number_format((float) $driver->salary_per_km, 3, ',', ' ') }} DT</div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Prime :</strong>
                    <div>{{ number_format((float) $driver->bonus, 3, ',', ' ') }} DT</div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-muted small">
        Créé le : {{ $driver->created_at?->format('d/m/Y H:i') ?? '—' }}
        <br>
        Dernière modification :
        {{ $driver->updated_at?->format('d/m/Y H:i') ?? '—' }}
    </div>
</div>
@endsection

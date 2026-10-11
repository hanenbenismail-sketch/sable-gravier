@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Modifier la mission {{ $mission->reference }}</h1>

        <a href="{{ route('missions.index') }}" class="btn btn-secondary">
            Retour
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Veuillez corriger les erreurs suivantes :</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('missions.update', $mission) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header">Informations générales</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="reference" class="form-label">Référence *</label>
                        <input type="text" name="reference" id="reference"
                               class="form-control"
                               value="{{ old('reference', $mission->reference) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="mission_date" class="form-label">Date de mission *</label>
                        <input type="date" name="mission_date" id="mission_date"
                               class="form-control"
                               value="{{ old('mission_date', $mission->mission_date?->format('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="mission_type" class="form-label">Type de mission *</label>
                        <select name="mission_type" id="mission_type" class="form-select" required>
                            <option value="delivery" @selected(old('mission_type', $mission->mission_type) === 'delivery')>Livraison</option>
                            <option value="transfer" @selected(old('mission_type', $mission->mission_type) === 'transfer')>Transfert</option>
                            <option value="return" @selected(old('mission_type', $mission->mission_type) === 'return')>Retour</option>
                            <option value="mixed" @selected(old('mission_type', $mission->mission_type) === 'mixed')>Mixte</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label">Statut *</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="planned" @selected(old('status', $mission->status) === 'planned')>Planifiée</option>
                            <option value="in_progress" @selected(old('status', $mission->status) === 'in_progress')>En cours</option>
                            <option value="completed" @selected(old('status', $mission->status) === 'completed')>Terminée</option>
                            <option value="cancelled" @selected(old('status', $mission->status) === 'cancelled')>Annulée</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="departure_time" class="form-label">Heure de départ</label>
                        <input type="datetime-local" name="departure_time" id="departure_time"
                               class="form-control"
                               value="{{ old('departure_time', $mission->departure_time?->format('Y-m-d\TH:i')) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Affectation des ressources</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="driver_id" class="form-label">Chauffeur *</label>
                        <select name="driver_id" id="driver_id" class="form-select" required>
                            <option value="">-- Choisir un chauffeur --</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}"
                                    @selected((string) old('driver_id', $mission->driver_id) === (string) $driver->id)>
                                    {{ $driver->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="truck_id" class="form-label">Camion *</label>
                        <select name="truck_id" id="truck_id" class="form-select" required>
                            <option value="">-- Choisir un camion --</option>
                            @foreach ($trucks as $truck)
                                <option value="{{ $truck->id }}"
                                    @selected((string) old('truck_id', $mission->truck_id) === (string) $truck->id)>
                                    {{ $truck->registration_number }} — {{ $truck->brand }} {{ $truck->model }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="departure_site_id" class="form-label">Site de départ *</label>
                        <select name="departure_site_id" id="departure_site_id" class="form-select" required>
                            <option value="">-- Choisir un site --</option>
                            @foreach ($sites as $site)
                                <option value="{{ $site->id }}"
                                    @selected((string) old('departure_site_id', $mission->departure_site_id) === (string) $site->id)>
                                    {{ $site->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Distance, quantité et revenus</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="total_distance" class="form-label">Distance totale (km)</label>
                        <input type="number" step="0.001" min="0"
                               name="total_distance" id="total_distance" class="form-control"
                               value="{{ old('total_distance', $mission->total_distance) }}">
                    </div>

                    <div class="col-md-4">
                        <label for="total_quantity" class="form-label">Quantité totale</label>
                        <input type="number" step="0.001" min="0"
                               name="total_quantity" id="total_quantity" class="form-control"
                               value="{{ old('total_quantity', $mission->total_quantity) }}">
                    </div>

                    <div class="col-md-4">
                        <label for="total_revenue" class="form-label">Revenu total (DT)</label>
                        <input type="number" step="0.001" min="0"
                               name="total_revenue" id="total_revenue" class="form-control"
                               value="{{ old('total_revenue', $mission->total_revenue) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Coûts de la mission</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="transport_cost" class="form-label">Transport (DT)</label>
                        <input type="number" step="0.001" min="0" name="transport_cost"
                               id="transport_cost" class="form-control"
                               value="{{ old('transport_cost', $mission->transport_cost) }}">
                    </div>

                    <div class="col-md-3">
                        <label for="fuel_cost" class="form-label">Carburant (DT)</label>
                        <input type="number" step="0.001" min="0" name="fuel_cost"
                               id="fuel_cost" class="form-control"
                               value="{{ old('fuel_cost', $mission->fuel_cost) }}">
                    </div>

                    <div class="col-md-3">
                        <label for="driver_cost" class="form-label">Chauffeur (DT)</label>
                        <input type="number" step="0.001" min="0" name="driver_cost"
                               id="driver_cost" class="form-control"
                               value="{{ old('driver_cost', $mission->driver_cost) }}">
                    </div>

                    <div class="col-md-3">
                        <label for="other_cost" class="form-label">Autres coûts (DT)</label>
                        <input type="number" step="0.001" min="0" name="other_cost"
                               id="other_cost" class="form-control"
                               value="{{ old('other_cost', $mission->other_cost) }}">
                    </div>
                </div>

                <p class="form-text mt-3 mb-0">
                    Le coût total et le bénéfice seront recalculés lors de l'enregistrement.
                </p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Notes</div>
            <div class="card-body">
                <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $mission->notes) }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
        <a href="{{ route('missions.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection

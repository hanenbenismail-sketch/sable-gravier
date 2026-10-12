@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Nouvelle mission</h1>
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

    <form action="{{ route('missions.store') }}" method="POST">
        @csrf

        {{-- Informations générales --}}
        <div class="card mb-4">
            <div class="card-header">Informations générales</div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="reference" class="form-label">Référence *</label>
                        <input type="text" name="reference" id="reference"
                               class="form-control" value="{{ old('reference') }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="mission_date" class="form-label">Date de mission *</label>
                        <input type="date" name="mission_date" id="mission_date"
                               class="form-control"
                               value="{{ old('mission_date', now()->toDateString()) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="mission_type" class="form-label">Type de mission *</label>
                        <select name="mission_type" id="mission_type"
                                class="form-select" required>
                            <option value="delivery" @selected(old('mission_type', 'delivery') === 'delivery')>
                                Livraison
                            </option>
                            <option value="transfer" @selected(old('mission_type') === 'transfer')>
                                Transfert
                            </option>
                            <option value="return" @selected(old('mission_type') === 'return')>
                                Retour
                            </option>
                            <option value="mixed" @selected(old('mission_type') === 'mixed')>
                                Mixte
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label">Statut *</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="planned" @selected(old('status', 'planned') === 'planned')>
                                Planifiée
                            </option>
                            <option value="in_progress" @selected(old('status') === 'in_progress')>
                                En cours
                            </option>
                            <option value="completed" @selected(old('status') === 'completed')>
                                Terminée
                            </option>
                            <option value="cancelled" @selected(old('status') === 'cancelled')>
                                Annulée
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="departure_time" class="form-label">Heure de départ</label>
                        <input type="datetime-local" name="departure_time"
                               id="departure_time" class="form-control"
                               value="{{ old('departure_time') }}">
                    </div>

                </div>
            </div>
        </div>

        {{-- Affectation des ressources --}}
        <div class="card mb-4">
            <div class="card-header">Affectation des ressources</div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="driver_id" class="form-label">Chauffeur *</label>
                        <select name="driver_id" id="driver_id"
                                class="form-select" required>
                            <option value="">-- Choisir un chauffeur --</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}"
                                    @selected((string) old('driver_id') === (string) $driver->id)>
                                    {{ $driver->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="truck_id" class="form-label">Camion *</label>
                        <select name="truck_id" id="truck_id"
                                class="form-select" required>
                            <option value="">-- Choisir un camion --</option>
                            @foreach ($trucks as $truck)
                                <option value="{{ $truck->id }}"
                                    @selected((string) old('truck_id') === (string) $truck->id)>
                                    {{ $truck->registration_number }} — {{ $truck->brand }} {{ $truck->model }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="departure_site_id" class="form-label">Site de départ *</label>
                        <select name="departure_site_id" id="departure_site_id"
                                class="form-select" required>
                            <option value="">-- Choisir un site --</option>
                            @foreach ($sites as $site)
                                <option value="{{ $site->id }}"
                                    @selected((string) old('departure_site_id') === (string) $site->id)>
                                    {{ $site->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                @if ($drivers->isEmpty())
                    <div class="alert alert-warning mt-3 mb-0">
                        Aucun chauffeur actif disponible. Ajoutez ou activez un chauffeur avant de créer une mission.
                    </div>
                @endif

                @if ($trucks->isEmpty())
                    <div class="alert alert-warning mt-3 mb-0">
                        Aucun camion enregistré. Ajoutez un camion avant de créer une mission.
                    </div>
                @endif

                @if ($sites->isEmpty())
                    <div class="alert alert-warning mt-3 mb-0">
                        Aucun site enregistré. Ajoutez un site avant de créer une mission.
                    </div>
                @endif
            </div>
        </div>

        {{-- Distance, quantité et revenus --}}
        <div class="card mb-4">
            <div class="card-header">Distance, quantité et revenus</div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="total_distance" class="form-label">Distance totale (km)</label>
                        <input type="number" step="0.001" min="0"
                               name="total_distance" id="total_distance"
                               class="form-control"
                               value="{{ old('total_distance', 0) }}">
                    </div>

                    <div class="col-md-4">
                        <label for="total_quantity" class="form-label">Quantité totale</label>
                        <input type="number" step="0.001" min="0"
                               name="total_quantity" id="total_quantity"
                               class="form-control"
                               value="{{ old('total_quantity', 0) }}">
                    </div>

                    <div class="col-md-4">
                        <label for="total_revenue" class="form-label">Revenu total (DT)</label>
                        <input type="number" step="0.001" min="0"
                               name="total_revenue" id="total_revenue"
                               class="form-control"
                               value="{{ old('total_revenue', 0) }}">
                    </div>

                </div>
            </div>
        </div>

        {{-- Coûts de la mission --}}
        <div class="card mb-4">
            <div class="card-header">Coûts de la mission</div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-3">
                        <label for="transport_cost" class="form-label">Transport (DT)</label>
                        <input type="number" step="0.001" min="0"
                               name="transport_cost" id="transport_cost"
                               class="form-control"
                               value="{{ old('transport_cost', 0) }}">
                    </div>

                    <div class="col-md-3">
                        <label for="fuel_cost" class="form-label">Carburant (DT)</label>
                        <input type="number" step="0.001" min="0"
                               name="fuel_cost" id="fuel_cost"
                               class="form-control"
                               value="{{ old('fuel_cost', 0) }}">
                    </div>

                    <div class="col-md-3">
                        <label for="driver_cost" class="form-label">Chauffeur (DT)</label>
                        <input type="number" step="0.001" min="0"
                               name="driver_cost" id="driver_cost"
                               class="form-control"
                               value="{{ old('driver_cost', 0) }}">
                    </div>

                    <div class="col-md-3">
                        <label for="other_cost" class="form-label">Autres coûts (DT)</label>
                        <input type="number" step="0.001" min="0"
                               name="other_cost" id="other_cost"
                               class="form-control"
                               value="{{ old('other_cost', 0) }}">
                    </div>

                </div>

                <p class="form-text mt-3 mb-0">
                    Le coût total et le bénéfice seront calculés automatiquement lors de l'enregistrement.
                </p>
            </div>
        </div>

        {{-- Arrêts de la mission --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Arrêts de la mission</span>
                <button type="button" class="btn btn-primary btn-sm" id="add-stop">
                    + Ajouter un arrêt
                </button>
            </div>

            <div class="card-body">
                <div id="stops-container"></div>
                <p class="form-text mb-0">
                    Ajoutez les arrêts dans l’ordre du trajet.
                    Le stock sera déduit lors de la confirmation du chargement.
                </p>
            </div>
        </div>

        <template id="stop-template">
            <div class="border rounded p-3 mb-3 mission-stop">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <strong class="stop-title">Arrêt</strong>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-stop">
                        Supprimer
                    </button>
                </div>

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Type d’arrêt</label>
                        <select class="form-select" data-field="stop_type" required>
                            <option value="loading">Chargement</option>
                            <option value="delivery">Livraison</option>
                            <option value="transfer">Transfert</option>
                            <option value="return">Retour</option>
                            <option value="unloading">Déchargement</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Site / carrière</label>
                        <select class="form-select" data-field="site_id">
                            <option value="">-- Choisir un site --</option>
                            @foreach ($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Client</label>
                        <select class="form-select" data-field="client_id">
                            <option value="">-- Choisir un client --</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Produit</label>
                        <select class="form-select" data-field="product_id">
                            <option value="">-- Choisir un produit --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Quantité</label>
                        <input type="number" class="form-control"
                               data-field="quantity" min="0.001"
                               step="0.001" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Unité</label>
                        <input type="text" class="form-control"
                               data-field="unit" value="tonne"
                               maxlength="50" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Distance depuis l’arrêt précédent (km)</label>
                        <input type="number" class="form-control"
                               data-field="distance_from_previous"
                               min="0" step="0.001" value="0">
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Notes de l’arrêt</label>
                        <input type="text" class="form-control"
                               data-field="notes">
                    </div>

                </div>
            </div>
        </template>

        {{-- Notes --}}
        <div class="card mb-4">
            <div class="card-header">Notes</div>
            <div class="card-body">
                <textarea name="notes" id="notes" rows="3"
                          class="form-control">{{ old('notes') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-success">
            Enregistrer la mission
        </button>

        <a href="{{ route('missions.index') }}" class="btn btn-secondary">
            Annuler
        </a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('stops-container');
    const template = document.getElementById('stop-template');
    const addButton = document.getElementById('add-stop');

    if (!container || !template || !addButton) {
        return;
    }

    function reindexStops() {
        container.querySelectorAll('.mission-stop').forEach(function (stop, index) {
            stop.querySelectorAll('[data-field]').forEach(function (field) {
                field.name = 'stops[' + index + '][' + field.dataset.field + ']';
            });

            stop.querySelector('.stop-title').textContent = 'Arrêt ' + (index + 1);
        });
    }

    function updateQuantityRequirement(stop) {
        const typeField = stop.querySelector('[data-field="stop_type"]');
        const quantityField = stop.querySelector('[data-field="quantity"]');
        const isLoading = typeField.value === 'loading';

        quantityField.required = !isLoading;
        quantityField.min = isLoading ? '0' : '0.001';

        if (isLoading && quantityField.value === '') {
            quantityField.value = '0';
        } else if (!isLoading && Number(quantityField.value) <= 0) {
            quantityField.value = '';
        }
    }

    function addStop() {
        const count = container.querySelectorAll('.mission-stop').length;

        if (count >= 30) {
            alert('Maximum 30 arrêts par mission.');
            return;
        }

        const fragment = template.content.cloneNode(true);
        const stop = fragment.querySelector('.mission-stop');

        stop.querySelectorAll('[data-field]').forEach(function (field) {
            field.name = 'stops[' + count + '][' + field.dataset.field + ']';
        });

        stop.querySelector('.remove-stop').addEventListener('click', function () {
            stop.remove();
            reindexStops();
        });

        stop.querySelector('[data-field="stop_type"]')
            .addEventListener('change', function () {
                updateQuantityRequirement(stop);
            });

        container.appendChild(fragment);
        reindexStops();
        updateQuantityRequirement(stop);
    }

    addButton.addEventListener('click', addStop);
    addStop();
});
</script>
@endsection

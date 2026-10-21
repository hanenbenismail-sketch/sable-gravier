<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Mission;
use App\Models\MissionStop;
use App\Models\Product;
use App\Models\Site;
use App\Models\Truck;
use App\Services\MissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class MissionServiceTest extends TestCase
{
    use RefreshDatabase;

    private function createMissionFixture(string $reference): array
    {
        $site = Site::create([
            'name' => 'Test Site',
            'type' => 'carriere',
            'gouvernorat' => 'Tozeur',
            'delegation' => 'Nefta',
        ]);

        $truck = Truck::create([
            'registration_number' => 'TRUCK-' . $reference,
            'capacity' => 20,
            'unit' => 'tonne',
        ]);

        $driver = Driver::create([
            'first_name' => 'Test',
            'last_name' => 'Driver',
            'license_number' => 'LICENSE-' . $reference,
        ]);

        $mission = Mission::create([
            'reference' => $reference,
            'truck_id' => $truck->id,
            'driver_id' => $driver->id,
            'departure_site_id' => $site->id,
            'mission_date' => now()->toDateString(),
            'mission_type' => 'delivery',
            'status' => 'planned',
        ]);

        return [$site, $mission];
    }

    public function test_mission_cannot_be_completed_with_pending_loading(): void
    {
        [$site, $mission] = $this->createMissionFixture(
            'TEST-MISSION-PENDING-001'
        );

        MissionStop::create([
            'mission_id' => $mission->id,
            'stop_order' => 1,
            'stop_type' => 'loading',
            'site_id' => $site->id,
            'quantity' => 10,
            'unit' => 'tonne',
            'loading_status' => 'pending',
        ]);

        try {
            app(MissionService::class)->completeMission($mission);
            $this->fail('La mission aurait dû être refusée.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame(
                'Tous les chargements doivent être confirmés avant la clôture.',
                $exception->getMessage()
            );
        }

        $this->assertSame('planned', $mission->fresh()->status);
    }

    public function test_delivery_cannot_exceed_quantity_loaded_for_product(): void
    {
        [$site, $mission] = $this->createMissionFixture(
            'TEST-MISSION-DELIVERY-001'
        );

        $product = Product::create([
            'name' => 'Test Sand',
            'code' => 'TEST-SAND-001',
            'type' => 'sable',
            'unit' => 'tonne',
        ]);

        MissionStop::create([
            'mission_id' => $mission->id,
            'stop_order' => 1,
            'stop_type' => 'loading',
            'site_id' => $site->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'unit' => 'tonne',
            'loading_status' => 'loaded',
        ]);

        $deliveryStop = MissionStop::create([
            'mission_id' => $mission->id,
            'stop_order' => 2,
            'stop_type' => 'delivery',
            'site_id' => $site->id,
            'product_id' => $product->id,
            'quantity' => 20,
            'unit' => 'tonne',
            'delivery_status' => 'pending',
        ]);

        try {
            app(MissionService::class)->deliverMissionStop(
                $deliveryStop,
                11
            );

            $this->fail('La livraison aurait dû être refusée.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame(
                'La quantité livrée dépasse la quantité chargée pour ce produit.',
                $exception->getMessage()
            );
        }

        $this->assertSame(
            '0.000',
            $deliveryStop->fresh()->delivered_quantity
        );

        $this->assertSame(
            'pending',
            $deliveryStop->fresh()->delivery_status
        );
    }

    public function test_mission_cannot_be_completed_with_pending_delivery_status(): void
    {
        [$site, $mission] = $this->createMissionFixture(
            'TEST-MISSION-PENDING-DELIVERY-001'
        );

        $site->update(['tracks_stock' => false]);

        $product = Product::create([
            'name' => 'Test Sand',
            'code' => 'TEST-SAND-PENDING-001',
            'type' => 'sable',
            'unit' => 'tonne',
        ]);

        MissionStop::create([
            'mission_id' => $mission->id,
            'stop_order' => 1,
            'stop_type' => 'loading',
            'site_id' => $site->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'unit' => 'tonne',
            'loading_status' => 'loaded',
        ]);

        MissionStop::create([
            'mission_id' => $mission->id,
            'stop_order' => 2,
            'stop_type' => 'delivery',
            'site_id' => $site->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'delivered_quantity' => 10,
            'unit' => 'tonne',
            'delivery_status' => 'pending',
        ]);

        try {
            app(MissionService::class)->completeMission($mission);
            $this->fail('La mission aurait dû être refusée.');
        } catch (InvalidArgumentException $exception) {
            $this->assertStringContainsString(
                'Impossible de terminer',
                $exception->getMessage()
            );
        }

        $this->assertNotSame('completed', $mission->fresh()->status);
    }

    public function test_store_calculates_total_quantity_from_delivery_stops(): void
    {
        [$site, $fixtureMission] = $this->createMissionFixture(
            'TEST-STORE-FIXTURE-' . uniqid()
        );

        $product = Product::create([
            'name' => 'Test Gravel',
            'code' => 'TEST-GRAVEL-' . uniqid(),
            'type' => 'gravier',
            'unit' => 'tonne',
        ]);

        $reference = 'TEST-STORE-POST-' . uniqid();

        $response = $this->post(route('missions.store'), [
            'reference' => $reference,
            'truck_id' => $fixtureMission->truck_id,
            'driver_id' => $fixtureMission->driver_id,
            'departure_site_id' => $site->id,
            'mission_date' => now()->toDateString(),
            'mission_type' => 'delivery',
            'status' => 'planned',

            // Cette valeur doit être ignorée par le calcul final.
            'total_quantity' => 999,

            'stops' => [
                [
                    'stop_type' => 'delivery',
                    'site_id' => $site->id,
                    'product_id' => $product->id,
                    'quantity' => '3.500',
                    'unit' => 'tonne',
                ],
                [
                    'stop_type' => 'delivery',
                    'site_id' => $site->id,
                    'product_id' => $product->id,
                    'quantity' => '2.250',
                    'unit' => 'tonne',
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();

        $mission = Mission::where('reference', $reference)->firstOrFail();

        $this->assertEquals(
            5.750,
            (float) $mission->total_quantity
        );

        $this->assertSame(
            2,
            $mission->stops()
                ->where('stop_type', 'delivery')
                ->count()
        );
    }
}

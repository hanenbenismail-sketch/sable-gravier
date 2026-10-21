<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Mission;
use App\Models\MissionStop;
use App\Models\Product;
use App\Models\Site;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Truck;
use App\Services\MissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissionReturnStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_returning_remaining_mission_stock_updates_destination_stock(): void
    {
        $source = Site::create([
            'name' => 'Return Test Source',
            'type' => 'carriere',
            'gouvernorat' => 'Tozeur',
            'delegation' => 'Nefta',
        ]);

        $destination = Site::create([
            'name' => 'Return Test Destination',
            'type' => 'depot',
            'gouvernorat' => 'Tozeur',
            'delegation' => 'Nefta',
        ]);

        $product = Product::create([
            'name' => 'Return Test Sand',
            'code' => 'RETURN-TEST-SAND',
            'type' => 'sable',
            'unit' => 'tonne',
        ]);

        $truck = Truck::create([
            'registration_number' => 'RETURN-TEST-TRUCK',
            'capacity' => 20,
            'unit' => 'tonne',
        ]);

        $driver = Driver::create([
            'first_name' => 'Return',
            'last_name' => 'Test',
            'license_number' => 'RETURN-TEST-LICENSE',
        ]);

        $mission = Mission::create([
            'reference' => 'RETURN-TEST-MISSION',
            'truck_id' => $truck->id,
            'driver_id' => $driver->id,
            'departure_site_id' => $source->id,
            'mission_date' => now()->toDateString(),
            'mission_type' => 'delivery',
            'status' => 'planned',
        ]);

        Stock::create([
            'site_id' => $source->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'reserved_quantity' => 0,
            'available_quantity' => 10,
            'last_updated' => now(),
        ]);

        $loadingStop = MissionStop::create([
            'mission_id' => $mission->id,
            'stop_order' => 1,
            'stop_type' => 'loading',
            'site_id' => $source->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'unit' => 'tonne',
            'loading_status' => 'pending',
        ]);

        $deliveryStop = MissionStop::create([
            'mission_id' => $mission->id,
            'stop_order' => 2,
            'stop_type' => 'delivery',
            'site_id' => $source->id,
            'product_id' => $product->id,
            'quantity' => 6,
            'unit' => 'tonne',
            'delivery_status' => 'pending',
        ]);

        $service = app(MissionService::class);

        $service->loadMissionStop($loadingStop, 10);
        $service->deliverMissionStop($deliveryStop, 6);

        $returnedMission = $service->returnMissionStock(
            $mission,
            $destination->id,
            4
        );

        $sourceStock = Stock::where('site_id', $source->id)
            ->where('product_id', $product->id)
            ->firstOrFail();

        $destinationStock = Stock::where('site_id', $destination->id)
            ->where('product_id', $product->id)
            ->firstOrFail();

        $this->assertEquals(0, (float) $sourceStock->quantity);
        $this->assertEquals(0, (float) $sourceStock->available_quantity);
        $this->assertEquals(4, (float) $destinationStock->quantity);
        $this->assertEquals(4, (float) $destinationStock->available_quantity);

        $outMovement = StockMovement::where('reference_type', 'mission_stop')
            ->where('reference_id', $loadingStop->id)
            ->where('type', 'OUT')
            ->firstOrFail();

        $this->assertEquals(10, (float) $outMovement->quantity);

        $returnMovement = StockMovement::where('reference_type', 'mission_return')
            ->where('reference_id', $mission->id)
            ->where('type', 'RETURN')
            ->firstOrFail();

        $this->assertEquals(4, (float) $returnMovement->quantity);
        $this->assertEquals($destination->id, $returnMovement->to_site_id);
        $this->assertEquals($destination->id, $returnedMission->return_site_id);
        $this->assertEquals(4, (float) $returnedMission->returned_quantity);

        // Vérifier que la mission se termine après le retour du stock.
        $completedMission = $service->completeMission($returnedMission);

        $this->assertSame('completed', $completedMission->status);
        $this->assertNotNull($completedMission->return_time);
    }
}

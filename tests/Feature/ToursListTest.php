<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToursListTest extends TestCase
{
    use RefreshDatabase;

    protected $api = '/api/v1/travels';

    /**
     * A basic feature test example.
     */
    public function test_tour_list_by_travel_slug_returns_correct_tours(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id]);

        $response = $this->get("$this->api/$travel->slug/tours");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tour->id]);
    }

    public function test_tour_price_is_shown_correctly(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 203.35]
        );

        $response = $this->get("$this->api/$travel->slug/tours");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['price' => '203.35']);
    }

    public function test_tours_list_returns_pagination(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory(15 + 1)->create(['travel_id' => $travel->id]);

        $response = $this->get("$this->api/$travel->slug/tours");

        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonPath('meta.last_page', 2);
    }

    public function test_tours_list_sorts_by_starting_date_correctly(): void
    {
        $travel = Travel::factory()->create();
        $laterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);
        $earlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now(),
            'ending_date' => now()->addDays(1),
        ]);

        $response = $this->get("$this->api/$travel->slug/tours");

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $earlierTour->id);
        $response->assertJsonPath('data.1.id', $laterTour->id);
    }

    public function test_tours_list_sorts_by_prices_correctly(): void
    {
        $travel = Travel::factory()->create();
        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 130,
        ]);
        $cheapLaterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 110,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);
        $cheapEarlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 108,
            'starting_date' => now(),
            'ending_date' => now()->addDays(1),
        ]);

        $response = $this->get("$this->api/$travel->slug/tours?sort_by=price&sort_order=asc");

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $cheapEarlierTour->id);
        $response->assertJsonPath('data.1.id', $cheapLaterTour->id);
        $response->assertJsonPath('data.2.id', $expensiveTour->id);
    }

    public function test_tours_list_filter_by_price_correctly(): void
    {
        $travel = Travel::factory()->create();
        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 130,
        ]);
        $cheapTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 123,
        ]);

        $endpoint = "$this->api/$travel->slug/tours";

        $response = $this->get("$endpoint?price_from=120");
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response = $this->get("$endpoint?price_from=125");
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response = $this->get("$endpoint?price_from=132");
        $response->assertJsonCount(0, 'data');

        $response = $this->get("$endpoint?price_to=130");
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);
    }

    public function test_tour_list_returns_validation_errors_filter_by_date(): void
    {
        $travel = Travel::factory()->create();

        $response = $this->getJson("$this->api/$travel->slug/tours?date_from=231-23-235");
        $response->assertStatus(422);

        $response = $this->getJson("$this->api/$travel->slug/tours?date_to=231-23-235");
        $response->assertStatus(422);
    }

    public function test_tour_list_returns_validation_errors_filter_by_price(): void
    {
        $travel = Travel::factory()->create();

        $response = $this->getJson("$this->api/$travel->slug/tours?price_from=twenty");
        $response->assertStatus(422);

        $response = $this->getJson("$this->api/$travel->slug/tours?price_to=twenty");
        $response->assertStatus(422);
    }

    public function test_tour_list_returns_validation_errors_filter_by_sorts(): void
    {
        $travel = Travel::factory()->create();

        $response = $this->getJson("$this->api/$travel->slug/tours?sort_by=prices");
        $response->assertStatus(422);
        $response->assertSeeTextInOrder(['price', 'name', 'ending_date']);

        $response = $this->getJson("$this->api/$travel->slug/tours?sort_order=ascs");
        $response->assertStatus(422);
        $response->assertSeeTextInOrder(['asc', 'desc']);
    }
}

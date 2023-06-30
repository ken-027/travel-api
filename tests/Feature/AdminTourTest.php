<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTourTest extends TestCase
{
    use RefreshDatabase;

    protected $api = '/api/v1/admin/travels';

    /**
     * A basic feature test example.
     */
    public function test_non_authenticated_accessing_posting_travel_validates(): void
    {
        $travel = Travel::factory()->create();
        $response = $this->postJson("$this->api/$travel->id/tours");

        $response->assertStatus(401);
    }

    public function test_non_admin_user_accessing_posting_travel_validates(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $travel = Travel::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));

        $response = $this->actingAs($user)->postJson("$this->api/$travel->id/tours");

        $response->assertStatus(403);
    }

    public function test_posting_travel_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $travel = Travel::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));

        $response = $this->actingAs($user)->postJson("$this->api/$travel->id/tours", [
            'name' => 'Travel name',
        ]);
        $response->assertStatus(422);

        $response = $this->actingAs($user)->postJson("$this->api/$travel->id/tours", [
            'name' => 'New Tours',
            'starting_date' => now(),
            'ending_date' => now()->addDay(),
            'price' => 136.3,
        ]);
        $response->assertStatus(201);

        $response = $this->get("/api/v1/travels/$travel->slug/tours");
        $response->assertJsonFragment(['name' => 'New Tours']);
    }
}

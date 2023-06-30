<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTravelTest extends TestCase
{
    use RefreshDatabase;

    protected $api = '/api/v1/admin/travels';

    /**
     * A basic feature test example.
     */
    public function test_non_authenticated_accessing_posting_travel_validates(): void
    {
        $response = $this->postJson($this->api);

        $response->assertStatus(401);
    }

    public function test_non_admin_user_accessing_posting_travel_validates(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));

        $response = $this->actingAs($user)->postJson($this->api);

        $response->assertStatus(403);
    }

    public function test_posting_travel_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));

        $response = $this->actingAs($user)->postJson($this->api, [
            'name' => 'Travel name',
        ]);
        $response->assertStatus(422);

        $response = $this->actingAs($user)->postJson($this->api, [
            'name' => 'New Travels',
            'description' => 'my new travels',
            'number_of_days' => 5,
            'is_public' => true,
        ]);
        $response->assertStatus(201);

        $response = $this->get('/api/v1/travels');
        $response->assertJsonFragment(['name' => 'New Travels']);
    }

    public function test_update_travel_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $travel = Travel::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));

        $response = $this->actingAs($user)->putJson("$this->api/$travel->id", [
            'name' => 'Travel name',
        ]);
        $response->assertStatus(422);

        $response = $this->actingAs($user)->putJson("$this->api/$travel->id", [
            'name' => 'New Travels Update',
            'description' => 'my new travels',
            'number_of_days' => 5,
            'is_public' => true,
        ]);
        $response->assertStatus(200);

        $response = $this->get('/api/v1/travels');
        $response->assertJsonFragment(['name' => 'New Travels Update']);
    }
}

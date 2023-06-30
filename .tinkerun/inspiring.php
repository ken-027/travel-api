$travel = Travel::create([
    'name' => 'travel philip',
    'description' => 'description',
    'number_of_days' => 5,
    'is_public' => true,
]);

Tour::create([
    'travel_id' => $travel->id,
    'name' => 'tour 1',
    'starting_date' => now()->subDays(10),
    'ending_date' => now()->addDays(15),
    'price' => '300.23',
]);
<?php

use App\Models\Tour;
use App\Models\Travel;
use App\Models\User;

$user = User::where('email', 'ken@email.com')->first();

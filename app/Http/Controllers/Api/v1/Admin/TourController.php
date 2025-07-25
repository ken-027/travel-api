<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;

class TourController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'role:admin']);
    }

    /**
     * Adding Tours by it's travel
     *
     * @authenticated
     */
    public function store(Travel $travel, TourRequest $request)
    {
        $tour = $travel->tours()->create($request->validated());

        return new TourResource($tour);
    }
}

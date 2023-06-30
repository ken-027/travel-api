<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTravelRequest;
use App\Http\Requests\UpdateTravelRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;

class TravelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role:admin')->only('store');
        $this->middleware('role:editor')->only('update');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @authenticated
     */
    public function store(StoreTravelRequest $request)
    {
        $travel = Travel::create($request->validated());

        return new TravelResource($travel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @authenticated
     */
    public function update(Travel $travel, UpdateTravelRequest $request)
    {
        $travel->update($request->validated());

        return new TravelResource($travel);
    }
}

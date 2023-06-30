<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListTourRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Travel $travel, ListTourRequest $request)
    {
        $tours = $travel->tours()
            ->when($request->get('price_from'), function ($query) use ($request) {
                $query->where('price', '>=', $request->get('price_from') * 100);
            })
            ->when($request->get('price_to'), function ($query) use ($request) {
                $query->where('price', '<=', $request->get('price_to') * 100);
            })
            ->when($request->get('date_from'), function ($query) use ($request) {
                $query->where('starting_date', '>=', $request->get('date_from'));
            })
            ->when($request->get('date_to'), function ($query) use ($request) {
                $query->where('starting_date', '<=', $request->get('date_to'));
            })
            ->when($request->get('sort_by') && $request->get('sort_order'), function ($query) use ($request) {
                $query->orderBy($request->get('sort_by'), $request->get('sort_order'));
            })
            ->orderBy('starting_date')
            ->paginate();

        return TourResource::collection($tours);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

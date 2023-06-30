<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class HeathCheckController extends Controller
{
    /**
     * Healthcheck
     *
     * Check that the service is up. If everything is okay, you'll get a 200 OK response.
     *
     * Otherwise, the request will fail with a 400 error, and a response listing the failed services.
     *
     * @response 500 scenario="Service is unhealthy" {"status": "down", "services": {"database": "up", "postgresql": "down"}}
     *
     * @responseField status The status of this API (`up` or `down`).
     * @responseField services Map of each downstream service and their status (`up` or `down`).
     */
    public function __invoke(Request $request)
    {
        try {

            Artisan::call('db:monitor');

            return response()->json([
                'status' => 'up',
                'services' => [
                    'database' => 'up',
                    'postgresql' => 'up',
                ],
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'down',
                'services' => [
                    'database' => 'down',
                    'postgresql' => $ex->getMessage(),
                ],
            ], 500);
        }
    }
}

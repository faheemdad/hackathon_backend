<?php

namespace App\Providers;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($message, $data = null) {
            $response = ['error' => false, 'message' => [$message] ];
            if (!empty($data)) $response = array_merge($response, ['data' => $data]);

            return response()->json($response, HttpResponse::HTTP_OK);
        });

        Response::macro('failed', function ($message, $status = 422) {
            $response = ['error' => true, 'errors' => [$message] ];
            return response()->json($response, $status);
        });

        Response::macro('unauthorized', function ($message = null) {
            return response()->json([
                'error' => true, 'errors' => [$message] ?: 'Unauthorized'
            ], HttpResponse::HTTP_UNAUTHORIZED);
        });

        Response::macro('forbidden', function ($message = null) {
            return response()->json([
                'error' => true, 'errors' => [$message] ?: 'Forbidden, operation not allowed'
            ], HttpResponse::HTTP_FORBIDDEN);
        });


        Response::macro('internalServerError', function ($message = null, $status = null) {
            return response()->json([
                'error' => true, 'errors' => [$message] ?: 'Internal Server Error',
            ], $status ?: HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        });

        Response::macro('globalError', function ($message = null, $status = null) {
            return response()->json([
                'error' => true, 'errors' => [$message] ?: 'Internal Server Error',
            ], $status ?: HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        });
    }
}

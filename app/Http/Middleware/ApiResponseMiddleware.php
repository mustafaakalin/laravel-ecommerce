<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            
            if (!isset($data['status'])) {
                $responseData = [
                    'status' => $response->getStatusCode() < 400 ? 'success' : 'error',
                    'data' => $data
                ];
                
                if ($response->getStatusCode() >= 400) {
                    $responseData['message'] = $data['message'] ?? 'An error occurred';
                    unset($responseData['data']);
                }
                
                $response->setData($responseData);
            }
        }

        return $response;
    }
}
<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Rate limiting por IP + ruta usando el throttler de CI4.
 * Uso en rutas: 'filter' => 'apiRate:CAPACIDAD,SEGUNDOS'
 * Ej: 'apiRate:5,60' = 5 request por minuto.
 */
class ApiRateLimitFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $capacity = (int)($arguments[0] ?? 120);
        $seconds  = (int)($arguments[1] ?? 60);

        // Clave por IP + URI para que el limite sea por endpoint
        $ip  = $request->getIPAddress();
        $uri = $request->getUri()->getPath();
        $key = 'api_' . md5($ip . '|' . $uri);

        $throttler = service('throttler');

        if (!$throttler->check($key, $capacity, $seconds)) {
            $retry = $throttler->getTokenTime($key, $capacity, $seconds) - time();
            return service('response')
                ->setStatusCode(429)
                ->setHeader('Retry-After', (string)max(1, $retry))
                ->setJSON([
                    'error' => 'Demasiadas solicitudes. Intenta de nuevo en unos segundos.',
                    'retry_after' => max(1, $retry),
                ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada que hacer
    }
}

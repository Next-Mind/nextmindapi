<?php

namespace App\Http\Middleware;

use App\Utils\Logger\Logger;

class CorsMiddleware
{

    private Logger $logger;

    public function handle($request, $next)
    {
        $this->logger = new Logger('CorsMiddleware');
        //CONFIGURAÇÕES DEFINIDAS NO .env
        $allowedOrigins = getenv('CORS_ALLOWED_ORIGINS') ?: '*';
        $allowedMethods = getenv('CORS_ALLOWED_METHODS') ?: 'GET, POST, PUT, DELETE, OPTIONS';
        $allowedHeaders = getenv('CORS_ALLOWED_HEADERS') ?: 'Content-Type, Authorization';
        $allowCredentials = getenv('CORS_ALLOW_CREDENTIALS') === 'true' ? 'true' : 'false';

        //CONFIGURAÇÕES DO CORS
        header("Access-Control-Allow-Origin: $allowedOrigins");
        header("Access-Control-Allow-Methods: $allowedMethods");
        header("Access-Control-Allow-Headers: $allowedHeaders");
        header("Access-Control-Allow-Credentials: $allowCredentials");

        $this->logger->debug("Método: {$_SERVER['REQUEST_METHOD']}");

        // Respondendo a requisições OPTIONS diretamente
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}

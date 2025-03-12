<?php

namespace App\Http\Middleware;

use App\Utils\Logger\Logger;

class Maintenance
{

    private Logger $logger;

    /**
     * Método responsável por executar o middleware
     *
     * @param  Request $request
     * @param  Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        $this->logger = new Logger('Maintenance');
        $this->logger->debug('Middleware acionado');

        //VERIFICA O ESTADO DE MANUTENÇÃO DA PÁGINA
        if (getenv('MAINTENANCE') == 'true') {
            throw new \Exception("Página em manutenção. Tente novamente mais tarde", 200);
        }

        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}

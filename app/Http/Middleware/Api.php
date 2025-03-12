<?php

namespace App\Http\Middleware;

use App\Utils\Logger\Logger;

class Api
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
        $this->logger = new Logger('ApiMiddleware');
        $this->logger->debug('Middleware raiz da API acionado.');
        //ALTERA O CONTENT TYPE PARA JSON
        $request->getRouter()->setContentType('application/json');



        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}

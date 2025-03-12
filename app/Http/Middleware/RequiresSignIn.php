<?php

namespace App\Http\Middleware;

use \App\Utils\Logger\Logger;

class RequiresSignIn
{
    private Logger $logger;

    public function handle($request, $next)
    {
        $this->logger = new Logger('RequiresSignInMiddleware');
        $this->logger->debug('Verificando se o usuário local está injetado na request');

        if (!isset($request->user)) {

            $this->logger->debug('Usuário não injetado na request, retornando HTTP CODE: 403');

            throw new \Exception('Cadastre-se na API antes de utilizar!', 403);
        }

        $this->logger->debug('Verificação concluída, executando próximo passo.');
        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}

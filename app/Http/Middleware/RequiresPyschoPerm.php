<?php

namespace App\Http\Middleware;

use \App\Utils\Logger\Logger;

class RequiresPyschoPerm
{
    private Logger $logger;

    public function handle($request, $next)
    {
        $this->logger = new Logger('RequiresPyschoPermMiddleware');
        $this->logger->debug('Verificando se o usuário logado tem permissão de Psicólogo');
        $user = $request->user;

        if ($user->user_type_id != 3) {

            $this->logger->debug('Usuário não possui permissões de psicólogo, retornando HTTP CODE: 403');

            throw new \Exception('Area Restrita para Psicologos!', 403);
        }

        $this->logger->debug('Verificação concluída, executando próximo passo.');
        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}

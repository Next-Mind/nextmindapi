<?php

namespace App\Http\Middleware;

use Kreait\Firebase\Factory;

use \App\Utils\Logger\Logger;

use \Exception;

class FirebaseAuth
{
    private Logger $logger = new Logger('FirebaseAuthMiddleware');
    private function getFirebaseAuthUser($request)
    {
        //INICIA A INSTÂNCIA DO FIREBASE
        $firebase = (new Factory)
            ->withServiceAccount(FIREBASE_KEY);

        $this->logger->debug('Instância do Firebase SDK Iniciada');

        $auth = $firebase->createAuth();

        $this->logger->debug('Instância do Firebase Auth Iniciada');

        //HEADERS
        $headers = $request->getHeaders();

        $this->logger->debug("Headers obtidos: " . json_encode($headers));

        //TOKEN PURO
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        try {
            $verifiedToken = $auth->verifyIdToken($token);
        } catch (Exception $e) {
            throw new Exception("Token inválido", 400);
        }

        $uid = $verifiedToken->headers()->get('sub');
        $user = $auth->getUser($uid);
        return $user;
    }

    private function auth($request)
    {
        //VERIFICA O USUÁRIO RECEBIDO
        if ($obUser = $this->getFirebaseAuthUser($request)) {
            $request->user = $obUser;
            return true;
        }

        //EMITE O ERRO DE TOKEN INVÁLIDO
        throw new Exception("Acesso negado", 403);
    }


    /**
     * Método responsável por executar o middleware
     *
     * @param  Request $request
     * @param  Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        $this->auth($request);

        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}

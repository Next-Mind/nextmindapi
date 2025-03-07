<?php

namespace App\Http\Middleware;

use Kreait\Firebase\Factory;

use \Exception;

class FirebaseAuth
{
    private function getFirebaseAuthUser($request)
    {
        //INICIA A INSTÂNCIA DO FIREBASE
        $firebase = (new Factory)
            ->withServiceAccount(FIREBASE_KEY)
            ->createAuth();

        //HEADERS
        $headers = $request->getHeaders();

        //TOKEN PURO
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        try {
            $verifiedToken = $firebase->verifyIdToken($token);
        } catch (Exception $e) {
            throw new Exception("Token inválido", 400);
        }

        $uid = $verifiedToken->headers()->get('sub');
        $user = $firebase->getUser($uid);
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

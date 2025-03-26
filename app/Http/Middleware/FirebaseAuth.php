<?php

namespace App\Http\Middleware;

use Kreait\Firebase\Factory;

use \App\Utils\Logger\Logger;
use \App\Model\Entity\Users\User as EntityUser;

use \Exception;

class FirebaseAuth
{
    private Logger $logger;

    private function injectLocalUser($request)
    {
        $email = $request->firebaseUser->email;
        $obUserEmail = EntityUser::getUserByEmail($email);
        if ($obUserEmail instanceof EntityUser) {
            $request->user = $obUserEmail;
        }
        return true;
    }

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

        $this->logger->debug('Iniciando verificação do token');

        try {
            /**
             * @var \Kreait\Firebase\JWT\Token\Plain $verifiedToken //Tem que deixar isso aqui senão minha IDE fica acusando que o método claims() não existe
             */
            $verifiedToken = $auth->verifyIdToken($token);
        } catch (Exception $e) {
            throw new Exception("Token inválido", 400);
        }

        $this->logger->debug('Obtendo UID do usuário');

        $uid = $verifiedToken->claims()->get('sub');
        $user = $auth->getUser($uid);

        $this->logger->debug("Usuário obtido | Email: {$user->email}");
        return $user;
    }

    private function auth($request)
    {
        //VERIFICA O USUÁRIO RECEBIDO
        if ($obUser = $this->getFirebaseAuthUser($request)) {
            $request->firebaseUser = $obUser;
            $this->injectLocalUser($request);
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
        $this->logger = new Logger('FirebaseAuthMiddleware');
        $this->auth($request);

        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}

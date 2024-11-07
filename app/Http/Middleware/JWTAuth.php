<?php

namespace App\Http\Middleware;

use \App\Model\Entity\User;
use Exception;
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

class JWTAuth{
        
    /**
     * Método responsável por retornar uma instância de usuário autenticado
     * @param Request $request
     * @return User
     */
    private function getJWTAuthUser($request){
        //HEADERS
        $headers = $request->getHeaders();
        
        //TOKEN PURO EM JWT
        $jwt = isset($headers['Authorization']) ? str_replace('Bearer ','',$headers['Authorization']) : '';
        
        try{
            //DECODE
            $decode = (array)JWT::decode($jwt,new Key(getenv('JWT_KEY'),'HS256'));
        }catch(Exception $e){
            throw new Exception("Token inválido",400);
        }

        //EMAIL
        $email = $decode['email'] ?? '';

        //BUSCA O USUÁRIO PELO E-MAIL
        $obUser = User::getUserByEmail($email);
        
        //RETORNA O USUARIO
        return $obUser instanceof User ? $obUser : false;
    }
    
    /**
     * Método responsável por validar o acesso via JWT
     * @param  Request $request
     */
    private function auth($request){
        //VERIFICA O USUÁRIO RECEBIDO
        if($obUser = $this->getJWTAuthUser($request)) {
            $request->user = $obUser;
            return true;
        }

        //EMITE O ERRO DE SENHA INVÁLIDA
        throw new Exception("Acesso negado", 403);
    }
        
    /**
     * Método responsável por executar o middleware
     *
     * @param  Request $request
     * @param  Closure $next
     * @return Response
     */
    public function handle($request,$next){
        //REALIZA A VALIDAÇÃO DO ACESSO VIA JWT
        $this->auth($request);

        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}
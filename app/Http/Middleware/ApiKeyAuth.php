<?php

namespace App\Http\Middleware;

use Closure;
use \App\Model\Entity\ApiKeys as EntityApiKeys;
use Exception;

class ApiKeyAuth {

    private $prefixMap = [
        'NXTM-' => 'mobile',
        'NXTD-' => 'desktop',
        'NXTW-' => 'web' 
    ];

    private function definePlatformByPrefix($apiKey) {
        $prefix = substr($apiKey,0,5);
        $platform = $this->prefixMap[$prefix] ?? null;
        return $platform;
    }
    
    /**
     * Método responsável por recuperar a hash da api key no banco de dados baseado na plataforma
     *
     * @param  string $platform
     * @return mixed
     */
    private function getStoredHashedApiKey($platform) {
        $obApiKey = EntityApiKeys::getApiKeyByPlatform($platform);
        return  $obApiKey instanceof EntityApiKeys ? $obApiKey : false;
    }

    
    /**
     * Método responsável por executar o middleware
     *
     * @param  Request $request
     * @param  Closure $next
     * @return Response
     */
    public function handle($request, Closure $next) {
        //OBTÉM OS HEADERS DA REQUISIÇÃO
        $headers = $request->getHeaders();

        //OBTÉM A CHAVE DE API
        $apiKey = $headers['x-api-key'] ?? '';

        //OBTÉM A PLATAFORMA BASEADA NO PREFIXO DA API KEY
        $platform = $this->definePlatformByPrefix($apiKey);

        //RECUPERA A HASH DA API KEY ARMAZENADA EM BANCO
        $storedHashedApiKey = $this->getStoredHashedApiKey($platform);

        //VALIDA A CHAVE DA API
        if(!$storedHashedApiKey || !password_verify($apiKey,$storedHashedApiKey->hash_api_key)) {
            throw new Exception("Api Key Invalida!",403);
        }

        return $next($request);
    }
}
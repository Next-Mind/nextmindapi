<?php

namespace App\Controller\Api;

class Api
{

    /**
     * Método responsável por retornar os detalhes da API
     *
     * @param  Request $request
     * @return array
     */
    public static function getDetails($request)
    {
        return [
            'nome' => 'API - NextMind',
            'versao' => 'v1.0.0',
            'autor' => 'Andre Custodio'
        ];
    }

    /**
     * Método responsável por formatar a resposta da API 
     *
     * @param  string $message
     * @param  mixed $data
     * @param  int $httpCode
     * @return array
     */
    protected static function getApiResponse(String $message, $data, $httpCode = 200)
    {
        return [
            'status' => 'success',
            'code' => $httpCode,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Método responsável por retornar os detalhes da paginação
     *
     * @param  Request $request
     * @param  Pagination $obPagination
     * @return array
     */
    protected static function getPagination($request, $obPagination)
    {
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //PÁGINA
        $pages = $obPagination->getPages();

        //RETORNO
        return [
            'paginaAtual' => isset($queryParams['page']) ? (int)$queryParams['page'] : 1,
            'quantidadePaginas' => !empty($pages) ? count($pages) : 1
        ];
    }
}

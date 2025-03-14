<?php

namespace App\Controller\Api;

use \App\Model\Entity\UserTypes as EntityUserTypes;
use Exception;
use \WilliamCosta\DatabaseManager\Pagination;

class UserTypes extends Api
{
    /**
     * Método responsável por obter a renderização dos itens de tipos de usuários
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUserTypeItems($request, &$obPagination)
    {
        //TIPOS DE USUÁRIOS
        $itens = [];

        //QUANTIDADE TOTAL DE REGISTRO
        $quantidadeTotal = EntityUserTypes::getUserTypes(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //QUANTIDADE POR PÁGINA
        $qtdPagina = $queryParams['results'] ?? 5;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, $qtdPagina);

        //RESULTADOS DA PÁGINA
        $results = EntityUserTypes::getUserTypes(null, 'id ASC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obUserType = $results->fetchObject(EntityUserTypes::class)) {
            //TIPOS DE USUÁRIO
            $itens[] = [
                'id' => $obUserType->id,
                'name' => $obUserType->name,
                'description' => $obUserType->description
            ];
        }

        //RETORNA OS TIPOS DE USUÁRIO DA LISTA
        return $itens;
    }

    /**
     * Método responsável por retornar a lista de tipos de usuário
     *
     * @param  Request $request
     * @return array
     */
    public static function getUserTypesList($request)
    {
        return parent::getApiResponse('Successful in retrieving the list of user types', [
            'user_types' => self::getUserTypeItems($request, $obPagination),
            'pagination'   => parent::getPagination($request, $obPagination)
        ]);
    }
}

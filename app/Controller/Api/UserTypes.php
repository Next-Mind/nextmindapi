<?php

namespace App\Controller\Api;

use \App\Model\Entity\UserTypes as EntityUserTypes;
use Exception;
use \WilliamCosta\DatabaseManager\Pagination;

class UserTypes extends Api {
    /**
     * Método responsável por obter a renderização dos itens de tipos de usuários
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUserTypeItems($request,&$obPagination){
        //TIPOS DE USUÁRIOS
        $itens = [];

        //QUANTIDADE TOTAL DE REGISTRO
        $quantidadeTotal = EntityUserTypes::getUserTypes(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //QUANTIDADE POR PÁGINA
        $qtdPagina = $queryParams['results'] ?? 5;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal,$paginaAtual,$qtdPagina);

        //RESULTADOS DA PÁGINA
        $results = EntityUserTypes::getUserTypes(null,'id ASC',$obPagination->getLimit());

        //RENDERIZA O ITEM
        while($obUserType = $results->fetchObject(EntityUserTypes::class)){
            //TIPOS DE USUÁRIO
            $itens[] = [
                'id' => $obUserType->id,
                'nome' => $obUserType->nome,
                'descricao' => $obUserType->descricao
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
    public static function getUserTypesList($request) {
        return [
            'tipos_usuario' => self::getUserTypeItems($request,$obPagination),
            'paginacao'   => parent::getPagination($request,$obPagination)
        ];
    }

    public static function setNewUserType($request){
        //POST VARS
        $postVars = $request->getPostVars();

        //VALIDA CAMPOS OBRIGATÓRIOS
        if(!isset($postVars["nome"]) && !isset($postVars["descricao"])) {
            throw new Exception("Os campos 'nome' e 'descricao' sao obrigatorios!",400);
        }

        //VALIDA SE O TIPO JÁ NÃO ESTÁ CADASTRADO
        $obUserTypeName = EntityUserTypes::getUserTypeByName($postVars["nome"]);
        if($obUserTypeName instanceof EntityUserTypes) {
            throw new Exception("Já existe um tipo de usuario com este nome cadastrado!");
        }
        
        //VALIDA SE O NOME É VALIDO
        if(is_numeric($postVars["nome"])) {
            throw new Exception("Nome não pode ser número!",400);
        }

        //INICIA INSTANCIA PARA CADASTRO
        $obUserType = new EntityUserTypes();
        $obUserType->nome = $postVars["nome"];
        $obUserType->descricao = $postVars["descricao"];
        $obUserType->cadastrar();

        //SUCESSO
        return [
            "message" => "success"
        ];
    }
}
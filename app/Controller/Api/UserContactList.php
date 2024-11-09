<?php

namespace App\Controller\Api;

use \App\Model\Entity\UserContactList as EntityUserContactList;
use \App\Model\Entity\User as EntityUser;
use Exception;
use \WilliamCosta\DatabaseManager\Pagination;

class UserContactList extends Api {

    /**
     * Método responsável por obter a renderização dos itens de usuários para a lista de contatos
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUserItems($request,&$obPagination){
        //Usuários
        $itens = [];

        //QUANTIDADE TOTAL DE REGISTRO
        $quantidadeTotal = EntityUserContactList::getContacts('usuario_id = '.$request->user->id,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //QUANTIDADE POR PÁGINA
        $qtdPagina = $queryParams['results'] ?? 5;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal,$paginaAtual,$qtdPagina);

        //RESULTADOS DA PÁGINA
        $results = EntityUserContactList::getContacts('usuario_id = '.$request->user->id,'data_adicao ASC',$obPagination->getLimit());

        //RENDERIZA O ITEM
        while($obUserContactList = $results->fetchObject(EntityUserContactList::class)){
            $obUser = EntityUser::getUserById($obUserContactList->contato_id);
            //USUÁRIOS
            $itens[] = [
                'id' => $obUser->id,
                'nome' => $obUserContactList->apelido,
                'email' => $obUser->email,
                'fone' => $obUser->fone1
            ];
        }

        //RETORNA OS USUÁRIOS DA LISTA
        return $itens;
        
    }
    
    /**
     * Método responsável por retornar a lista de contatos do usuário atualmente logado
     *
     * @param  Request $request
     * @return array
     */
    public static function getUsersContactList($request) {
        return [
            'usuários' => self::getUserItems($request,$obPagination),
            'paginacao'   => parent::getPagination($request,$obPagination)
        ];
    }

    public static function setNewContact($request) {
        //OBTÉM VARIÁVEIS DO POST
        $postVars = $request->getPostVars();

        //VALIDA SE FOI DIGITADO UM NÚMERO
        if(!is_numeric($postVars["usuario_id"])) {
            throw new Exception("Por gentileza, digite um numero valido!",400);
        }

        //VALIDA CAMPOS OBRIGATÓRIOS
        if(!isset($postVars["usuario_id"])) {
            throw new Exception("O campo 'usuario_id' é obrigatório!",400);
        }

        if(!isset($postVars["apelido"])) {
            throw new Exception("O campo 'apelido' é obrigatório!",400);
        }

        //VALIDA SE O ID INFORMADO NO POST É IGUAL AO ID DO USUÁRIO LOGADO
        if($request->user->id == $postVars["usuario_id"]) {
            throw new Exception('Voce nao pode se adicionar a sua lista de contatos!',400);
        }

        //VALIDA SE O USUÁRIO INFORMADO EXISTE
        $obUser = EntityUser::getUserById($postVars["usuario_id"]);
        if(!$obUser instanceof EntityUser){
            throw new Exception('Este usuario nao existe!',400);
        }

        //VALIDA SE JÁ ESTÁ NA LISTA DE CONTATOS
        $hasUserInContactList = EntityUserContactList::isUserInContactList($request->user->id,$postVars["usuario_id"]);
        if($hasUserInContactList instanceof EntityUserContactList) {
            throw new Exception('Este usuario ja na lista de contatos do user logado!',400);
        }

        //NOVO CONTATO
        $obUserContact = new EntityUserContactList();
        $obUserContact->usuario_id = $request->user->id;
        $obUserContact->contato_id = $postVars["usuario_id"];
        $obUserContact->apelido = $postVars["apelido"];
        $obUserContact->cadastrar();

        return [
            'message' => 'success'
        ];
    }

    public static function setDeleteContact($request) {
        //OBTÉM VARIÁVEIS DO POST
        $postVars = $request->getPostVars();

        //VALIDA SE FOI DIGITADO UM NÚMERO
        if(!is_numeric($postVars["usuario_id"])) {
            throw new Exception("Por gentileza, digite um numero valido!",400);
        }

        //VALIDA CAMPOS OBRIGATÓRIOS
        if(!isset($postVars["usuario_id"])) {
            throw new Exception("O campo 'usuario_id' é obrigatório!",400);
        }

        //VALIDA SE O USUÁRIO INFORMADO EXISTE NA LISTA DE CONTATOS
        $obUserContactList = EntityUserContactList::isUserInContactList($request->user->id,$postVars["usuario_id"]);
        if(!$obUserContactList instanceof EntityUserContactList){
            throw new Exception('Este usuario nao existe na lista de contatos!',400);
        }

        //INATIVA O REGISTRO NO BANCO
        $obUserContactList->deletar();

        //SUCESSO
        return [
            "message" => "success"
        ];
    }

    public static function setEditContact($request){
        //OBTÉM AS VARIÁVEIS DO POST
        $postVars = $request->getPostVars();

        //VALIDA SE FOI DIGITADO UM NÚMERO
        if(!is_numeric($postVars["usuario_id"])) {
            throw new Exception("Por gentileza, digite um numero valido!",400);
        }

        //VALIDA CAMPOS OBRIGATÓRIOS
        if(!isset($postVars["usuario_id"])) {
            throw new Exception("O campo 'usuario_id' é obrigatório!",400);
        }

        if(!isset($postVars["apelido"])) {
            throw new Exception("O campo 'apelido' é obrigatório!",400);
        }
        
        //VALIDA QUANTIDADE DE CARACTERES DIGITADOS PARA O APELIDO
        if(strlen($postVars["apelido"]) > 45){
            throw new Exception("O máximo de caracteres para o campo 'apelido' é de 45 caracteres!",400);
        }
        
        //VALIDA SE O USUÁRIO INFORMADO EXISTE NA LISTA DE CONTATOS
        $obUserContactList = EntityUserContactList::isUserInContactList($request->user->id,$postVars["usuario_id"]);
        if(!$obUserContactList instanceof EntityUserContactList){
            throw new Exception('Este usuario nao existe na lista de contatos!',400);
        }

        $obUserContactList->apelido = $postVars["apelido"];
        $obUserContactList->atualizar();
        
        return [
            "message" : "success"
        ];
    }
}

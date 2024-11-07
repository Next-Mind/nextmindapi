<?php

namespace App\Controller\Api;

use \App\Model\Entity\User as EntityUser;
use Exception;
use \WilliamCosta\DatabaseManager\Pagination;

class User extends Api{

    /**
     * Método responsável por obter a renderização dos itens de usuários para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUserItems($request,&$obPagination){
        //UsuárioS
        $itens = [];

        //QUANTIDADE TOTAL DE REGISTRO
        $quantidadeTotal = EntityUser::getUsers(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //QUANTIDADE POR PÁGINA
        $qtdPagina = $queryParams['results'] ?? 5;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadeTotal,$paginaAtual,$qtdPagina);

        //RESULTADOS DA PÁGINA
        $results = EntityUser::getUsers(null,'id ASC',$obPagination->getLimit());

        //RENDERIZA O ITEM
        while($obUser = $results->fetchObject(EntityUser::class)){
            //USUÁRIOS
            $itens[] = [
                'id' => (int)$obUser ->id,
                'nome' => $obUser ->nome,
                'email' => $obUser->email
            ];
        }

        //RETORNA OS USUÁRIOS
        return $itens;
        
    }
    
    /**
     * Método responsável por retornar os usuários cadastrados
     *
     * @param  Request $request
     * @return array
     */
    public static function getUsers($request){
        return [
            'usuários' => self::getUserItems($request,$obPagination),
            'paginacao'   => parent::getPagination($request,$obPagination)
        ];
    }
    
    /**
     * Método responsável por retornar os detalhes de um usuário
     *
     * @param  Request $request
     * @param  int $id
     * @return array
     */
    public static function getUser($request,$id){
        //VALIDA O ID DO USUÁRIO
        if(!is_numeric($id)){
            throw new \Exception("O id '".$id."' Não é válido",400);
        }


        //BUSCA USUÁRIO
        $obUser = EntityUser::getUserById($id);

        //VALIDA SE O USUÁRIO EXISTE
        if(!$obUser instanceof EntityUser) {
            throw new \Exception("O Usuário ".$id." Não foi encontrado",404);
        }

        //RETORNA OS DETALHES DO USUÁRIO
        return [
            'id' => (int)$obUser ->id,
            'nome' => $obUser ->nome,
            'email' => $obUser->email
        ];
    }
    
    /**
     * Método responsável por retornar o usuário atualmente conectado
     *
     * @param  Request $request
     * @return array
     */
    public static function getCurrentUser($request){
        //USUARIO ATUAL
        $obUser = $request->user;

        return [
            'id' => (int)$obUser ->id,
            'nome' => $obUser ->nome,
            'email' => $obUser->email
        ];
    }
    
    /**
     * Método responsável por cadastrar um novo Usuário
     *
     * @param  Request $request
     * @return array
     */
    public static function setNewUser($request){
        //POST VARS
        $postVars = $request->getPostVars();

        //VALIDA OS CAMPOS OBRIGATÓRIOS
        if(!isset($postVars['nome']) or !isset($postVars['email']) or !isset($postVars['senha'])){
            throw new Exception("Os campos 'nome' e 'email' e 'senha' são obrigatórios",400);
        }

        //VALIDA A DUPLICAÇÃO DE USUÁRIOS
        $obUserEmail = EntityUser::getUserByEmail($postVars['email']);
        if($obUserEmail instanceof EntityUser) {
            throw new Exception("O e-mail ".$postVars['email']." já está em uso",400);
        }

        //NOVO USUÁRIO
        $obUser = new EntityUser;
        $obUser->nome = $postVars['nome'];
        $obUser->email = $postVars['email'];
        $obUser->senha = password_hash($postVars['senha'],PASSWORD_DEFAULT);
        $obUser->cadastrar();

        //RETORNA OS DETALHES DO USUÁRIO CADASTRADO
        return [
            'id' => (int)$obUser ->id,
            'nome' => $obUser ->nome,
            'email' => $obUser->email
        ];
    }
    
    /**
     * Método responsável por atualizar um Usuário
     *
     * @param  Request $request
     * @param  int $int
     * @return array
     */
    public static function setEditUser($request,$id){
        //POST VARS
        $postVars = $request->getPostVars();

        //VALIDA OS CAMPOS OBRIGATÓRIOS
        if(!isset($postVars['nome']) or !isset($postVars['email']) or !isset($postVars['senha'])){
            throw new Exception("Os campos 'nome' e 'email' e 'senha' são obrigatórios",400);
        }

        //BUSCA O USUÁRIO NO BANCO
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTANCIA
        if(!$obUser instanceof EntityUser) {
            throw new \Exception("O Usuário ".$id." Não foi encontrado",404);
        }

        //VALIDA A DUPLICAÇÃO DE USUÁRIOS
        $obUserEmail = EntityUser::getUserByEmail($postVars['email']);
        if($obUserEmail instanceof EntityUser && $obUserEmail->id != $obUser->id) {
            throw new Exception("O e-mail ".$postVars['email']." já está em uso",400);
        } 

        //ATUALIZA O USUÁRIO
        $obUser->nome = $postVars['nome'];
        $obUser->email = $postVars['email'];
        $obUser->senha = password_hash($postVars['senha'],PASSWORD_DEFAULT);
        $obUser->atualizar();

        //RETORNA OS DETALHES DO USUÁRIO ATUALIZADO
        return [
            'id' => (int)$obUser ->id,
            'nome' => $obUser ->nome,
            'email' => $obUser->email
        ];
    }
    
    /**
     * Método responsável por excluir um Usuário
     *
     * @param  Request $request
     * @param  int $int
     * @return array
     */
    public static function setDeleteUser($request,$id){
        //BUSCA O Usuário NO BANCO
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTANCIA
        if(!$obUser instanceof EntityUser) {
            throw new \Exception("O Usuário ".$id." Não foi encontrado",404);
        }

        //IMPEDE A EXCLUSÃO DO PRÓPRIO CADASTRO
        if($obUser->id == $request->user->id){
            throw new Exception("Não é possível excluir um cadastro atualmente conectado");
        }

        //INATIVA O USUÁRIO
        $obUser->inativar();

        //RETORNA O SUCESSO DA EXCLUSAO
        return [
            'sucesso' => 'true'
        ];
    }

}
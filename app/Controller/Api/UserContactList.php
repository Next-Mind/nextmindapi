<?php

namespace App\Controller\Api;

use \App\Model\Entity\Users\UserContactList as EntityUserContactList;
use \App\Model\Entity\Users\User as EntityUser;
use Exception;
use \WilliamCosta\DatabaseManager\Pagination;

class UserContactList extends Api
{

    /**
     * Método responsável por obter a renderização dos itens de usuários para a lista de contatos
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUserItems($request, &$obPagination)
    {
        //Usuários
        $itens = [];

        //QUANTIDADE TOTAL DE REGISTRO
        $totalLength = EntityUserContactList::getContacts('user_id = ' . $request->user->id, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //QUANTIDADE POR PÁGINA
        $qtdPagina = $queryParams['results'] ?? 5;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($totalLength, $paginaAtual, $qtdPagina);

        //RESULTADOS DA PÁGINA
        $results = EntityUserContactList::getContacts('user_id = ' . $request->user->id, 'created_at ASC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obUserContactList = $results->fetchObject(EntityUserContactList::class)) {
            $obUser = EntityUser::getUserById($obUserContactList->contact_id);
            //USUÁRIOS
            $itens[] = [
                'id' => $obUser->id,
                'name' => $obUserContactList->nickname,
                'email' => $obUser->email,
                'phone' => $obUser->phone1
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
    public static function getUsersContactList($request)
    {
        return parent::getApiResponse('Successful return to contact list', [
            'users' => self::getUserItems($request, $obPagination),
            'pagination'   => parent::getPagination($request, $obPagination)
        ]);
    }

    /**
     * Método responsável por adicionar um novo contato a lista do usuário atualmente logado
     *
     * @param  Request $request
     * @return array
     */
    public static function setNewContact($request)
    {
        //OBTÉM VARIÁVEIS DO POST
        $postVars = $request->getPostVars();

        //VALIDA SE FOI DIGITADO UM NÚMERO
        if (!is_numeric($postVars["contact_id"])) {
            throw new Exception("Please enter a valid number!", 400);
        }

        //VALIDA CAMPOS OBRIGATÓRIOS
        if (!isset($postVars["contact_id"])) {
            throw new Exception("The 'contact_id' field is required!", 400);
        }

        if (!isset($postVars["nickname"])) {
            throw new Exception("The 'nickname' field is required!", 400);
        }

        //VALIDA SE O ID INFORMADO NO POST É IGUAL AO ID DO USUÁRIO LOGADO
        if ($request->user->id == $postVars["contact_id"]) {
            throw new Exception("You can't add yourself to your contact list!", 400);
        }

        //VALIDA SE O USUÁRIO INFORMADO EXISTE
        $obUser = EntityUser::getUserById($postVars["contact_id"]);
        if (!$obUser instanceof EntityUser) {
            throw new Exception("This user doesn't exist!", 400);
        }

        //VALIDA SE JÁ ESTÁ NA LISTA DE CONTATOS
        $hasUserInContactList = EntityUserContactList::isUserInContactList($request->user->id, $postVars["contact_id"]);
        if ($hasUserInContactList instanceof EntityUserContactList) {
            throw new Exception("This user is already in the logged-in user's contact list!", 400);
        }

        //NOVO CONTATO
        $obUserContact = new EntityUserContactList();
        $obUserContact->user_id = $request->user->id;
        $obUserContact->contact_id = $postVars["contact_id"];
        $obUserContact->nickname = $postVars["nickname"];
        $obUserContact->register();

        return parent::getApiResponse('Successful in adding the user to the contact list', $obUserContact, 201);
    }

    /**
     * Método responsável por remover o contato da lista de contatos do usuário atualmente logado
     *
     * @param  Request $request
     * @return array
     */
    public static function setDeleteContact($request)
    {
        //OBTÉM VARIÁVEIS DO POST
        $postVars = $request->getPostVars();

        //VALIDA SE FOI DIGITADO UM NÚMERO
        if (!is_numeric($postVars["contact_id"])) {
            throw new Exception("Please enter a valid number!", 400);
        }

        //VALIDA CAMPOS OBRIGATÓRIOS
        if (!isset($postVars["contact_id"])) {
            throw new Exception("The 'contact_id' field is required!", 400);
        }

        //VALIDA SE O USUÁRIO INFORMADO EXISTE NA LISTA DE CONTATOS
        $obUserContactList = EntityUserContactList::isUserInContactList($request->user->id, $postVars["contact_id"]);
        if (!$obUserContactList instanceof EntityUserContactList) {
            throw new Exception("This user doesn't exist in the contact list!", 400);
        }

        //INATIVA O REGISTRO NO BANCO
        $obUserContactList->deletar();

        //SUCESSO
        return parent::getApiResponse('Successful removal of user from contact list', []);
    }

    /**
     * Método responsável por editar o contato selecionado
     *
     * @param  Request $request
     * @return array
     */
    public static function setEditContact($request)
    {
        //OBTÉM AS VARIÁVEIS DO POST
        $postVars = $request->getPostVars();

        //VALIDA SE FOI DIGITADO UM NÚMERO
        if (!is_numeric($postVars["user_id"])) {
            throw new Exception("Please enter a valid number!", 400);
        }

        //VALIDA CAMPOS OBRIGATÓRIOS
        if (!isset($postVars["contact_id"])) {
            throw new Exception("The 'contact_id' field is required!", 400);
        }

        if (!isset($postVars["nickname"])) {
            throw new Exception("The nickname field is required!", 400);
        }

        //VALIDA QUANTIDADE DE CARACTERES DIGITADOS PARA O nickname
        if (strlen($postVars["nickname"]) > 45) {
            throw new Exception("The maximum number of characters for the 'nickname' field is 45!", 400);
        }

        //VALIDA SE O USUÁRIO INFORMADO EXISTE NA LISTA DE CONTATOS
        $obUserContactList = EntityUserContactList::isUserInContactList($request->user->id, $postVars["contact_id"]);
        if (!$obUserContactList instanceof EntityUserContactList) {
            throw new Exception("This user doesn't exist in the contact list!", 400);
        }

        $obUserContactList->nickname = $postVars["nickname"];
        $obUserContactList->update();

        return parent::getApiResponse('Successful in editing the contact', $obUserContactList);
    }
}

<?php

use \App\Http\Response;
use \App\Controller\Api;

$userContactMiddlewares = [
    'requires-sign-in'
];

//ROTA DE LISTAGEM DOS USUÁRIOS ELEGÍVEIS PARA ADICIONAR NA LISTA
$obRouter->get('/v1/contacts/users', [
    'middlewares' => $userContactMiddlewares,
    function ($request) {
        return new Response(200, Api\UserContactList::getEligibleUsersForContactList($request), 'application/json');
    }
]);

//ROTA DE LISTAGEM DOS CONTATOS DO USUÁRIO
$obRouter->get('/v1/contacts', [
    'middlewares' => $userContactMiddlewares,
    function ($request) {
        return new Response(200, Api\UserContactList::getUsersContactList($request), 'application/json');
    }
]);

//ROTA DE CADASTRO DE USUÁRIO NA LISTA DE CONTATOS DO USER
$obRouter->post('/v1/contacts', [
    'middlewares' => $userContactMiddlewares,
    function ($request) {
        return new Response(201, Api\UserContactList::setNewContact($request), 'application/json');
    }
]);

//ROTA DE ATUALIZAÇÃO DE CONTATO
$obRouter->put('/v1/contacts', [
    'middlewares' => $userContactMiddlewares,
    function ($request) {
        return new Response(200, Api\UserContactList::setEditContact($request), 'application/json');
    }
]);

//ROTA DE EXCLUSÃO DO USUÁRIO NA LISTA DE CONTATOS DO USER
$obRouter->delete('/v1/contacts', [
    'middlewares' => $userContactMiddlewares,
    function ($request) {
        return new Response(200, Api\UserContactList::setDeleteContact($request), 'application/json');
    }
]);

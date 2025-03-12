<?php

use \App\Http\Response;
use \App\Controller\Api;

$userContactMiddlewares = [
    'requires-sign-in'
];

//ROTA DE LISTAGEM DOS CONTATOS DO USUÁRIO
$obRouter->get('/api/v1/contacts', [
    'middlewares' => $userContactMiddlewares,
    function ($request) {
        return new Response(200, Api\UserContactList::getUsersContactList($request), 'application/json');
    }
]);

//ROTA DE CADASTRO DE USUÁRIO NA LISTA DE CONTATOS DO USER
$obRouter->post('/api/v1/contacts', [
    'middlewares' => $userContactMiddlewares,
    function ($request) {
        return new Response(201, Api\UserContactList::setNewContact($request), 'application/json');
    }
]);

//ROTA DE ATUALIZAÇÃO DE CONTATO
$obRouter->put('/api/v1/contacts', [
    'middlewares' => $userContactMiddlewares,
    function ($request) {
        return new Response(200, Api\UserContactList::setEditContact($request), 'application/json');
    }
]);

//ROTA DE EXCLUSÃO DO USUÁRIO NA LISTA DE CONTATOS DO USER
$obRouter->delete('/api/v1/contacts', [
    'middlewares' => $userContactMiddlewares,
    function ($request) {
        return new Response(200, Api\UserContactList::setDeleteContact($request), 'application/json');
    }
]);

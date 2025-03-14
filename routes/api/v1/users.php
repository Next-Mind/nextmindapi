<?php

use \App\Http\Response;
use \App\Controller\Api;

$usersMiddlewares =  [
    'requires-sign-in'
];


//ROTA PARA RETORNAR OS DADOS DO USUÁRIO ATUALMENTE CONECTADO
$obRouter->get('/api/v1/users/me', [
    'middlewares' => $usersMiddlewares,
    function ($request) {
        return new Response(200, Api\User::getCurrentUser($request), 'application/json');
    }
]);

//ROTA PARA ATUALIZAÇÃO DOS DADOS INICIAis DE PERFIL DO USUÁRIO ATUALMENTE CONECTADO
$obRouter->put('/api/v1/users/me/profile', [
    'middlewares' => $usersMiddlewares,
    function ($request) {
        return new Response(201, Api\User::setEditLazyUser($request), 'application/json');
    }
]);

//ROTA PARA ATUALIZAÇÃO DOS DADOS DE QUESTIONÁRIO OBRIGATÓRIO DO USUÁRIO ATUALMENTE CONECTADO
$obRouter->put('/api/v1/users/me/questionnaire', [
    'middlewares' => $usersMiddlewares,
    function ($request) {
        return new Response(201, Api\User::setEditQuestUser($request), 'application/json');
    }
]);

//ROTA PARA ATUALIZAÇÃO DO ENDEREÇO DO USUÁRIO ATUALMENTE CONECTADO
$obRouter->put('/api/v1/users/me/address', [
    'middlewares' => $usersMiddlewares,
    function ($request) {
        return new Response(201, Api\User::setEditAddressUser($request), 'application/json');
    }
]);

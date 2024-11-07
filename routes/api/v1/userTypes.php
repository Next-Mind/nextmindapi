<?php

use \App\Http\Response;
use \App\Controller\Api;

$obRouter->get('/api/v1/users/type',[
    'middlewares' => [
        'api',
        'jwt-auth'
    ],
    function ($request) {
        return new Response(200,Api\UserTypes::getUserTypesList($request),'application/json');
    }
]);

$obRouter->post('/api/v1/users/type',[
    'middlewares' => [
        'api',
        'jwt-auth'
    ],
    function ($request) {
        return new Response(200, Api\UserTypes::setNewUserType($request),'application/json');
    }
]);
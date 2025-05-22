<?php

use \App\Http\Response;
use \App\Controller\Api;

$obRouter->get('/v1/users/type', [
    'middlewares' => [],
    function ($request) {
        return new Response(200, Api\UserTypes::getUserTypesList($request), 'application/json');
    }
]);

// $obRouter->post('/v1/users/type', [
//     'middlewares' => [],
//     function ($request) {
//         return new Response(200, Api\UserTypes::setNewUserType($request), 'application/json');
//     }
// ]);

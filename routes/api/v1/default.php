<?php

use \App\Http\Response;
use \App\Controller\Api;

//ROTA RAIZ DA API
$obRouter->get('/v1', [
    'middlewares' => [],
    function ($request) {
        return new Response(200, Api\Api::getDetails($request), 'application/json');
    }
]);

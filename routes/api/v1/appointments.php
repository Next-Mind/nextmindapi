<?php

use \App\Http\Response;
use \App\Controller\Api;

//ROTA PARA INSERIR NOVAS DISPONIBILIDADES
$obRouter->post('/api/v1/availabilities', [
    'middlewares' => [
        'requires-sign-in',
        'requires-psychologist-permission'
    ],
    function ($request) {
        $response =  Api\Appointments\Availabilites::setNewAvailability($request);
        return new Response($response['code'], $response, 'application/json');
    }
]);

//ROTA PARA CONSULTAR DISPONIBILIDADES
$obRouter->get('/api/v1/availabilities', [
    'middlewares' => [
        'requires-sign-in'
    ],
    function ($request) {
        $response = Api\Appointments\Availabilites::getAvailabilities($request);
        return new Response($response['code'], $response, 'application/json');
    }
]);

$obRouter->put('/api/v1/availabilities', [
    'middlewares' => [
        'requires-sign-in',
        'requires-psychologist-permission'
    ],
    function ($request) {
        $response = Api\Appointments\Availabilites::editAvailabilityStatus($request);
        return new Response($response['code'], $response, 'application/json');
    }
]);

$obRouter->put('/api/v1/availabilities/reserve', [
    'middlewares' => [
        'requires-sign-in',
    ],
    function ($request) {
        $response = Api\Appointments\Availabilites::scheduleAvailabilityByUser($request);
        return new Response($response['code'], $response, 'application/json');
    }
]);

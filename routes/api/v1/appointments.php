<?php

use \App\Http\Response;
use \App\Controller\Api;

//ROTA PARA INSERIR NOVAS DISPONIBILIDADES
$obRouter->post('/v1/availabilities', [
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
$obRouter->get('/v1/availabilities', [
    'middlewares' => [
        'requires-sign-in'
    ],
    function ($request) {
        $response = Api\Appointments\Availabilites::getAvailabilities($request);
        return new Response($response['code'], $response, 'application/json');
    }
]);

$obRouter->put('/v1/availabilities', [
    'middlewares' => [
        'requires-sign-in',
        'requires-psychologist-permission'
    ],
    function ($request) {
        $response = Api\Appointments\Availabilites::editAvailabilityStatus($request);
        return new Response($response['code'], $response, 'application/json');
    }
]);

$obRouter->put('/v1/availabilities/reserve', [
    'middlewares' => [
        'requires-sign-in',
    ],
    function ($request) {
        $response = Api\Appointments\Availabilites::scheduleAvailabilityByUser($request);
        return new Response($response['code'], $response, 'application/json');
    }
]);

$obRouter->post('/v1/appointments', [
    'middlewares' => [
        'requires-sign-in',
    ],
    function ($request) {
        $response = Api\Appointments\Appointments::setNewAppointment($request);
        return new Response($response['code'], $response, 'application/json');
    }
]);

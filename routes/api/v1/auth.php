<?php

use \App\Http\Response;
use \App\Controller\Api;

//ROTA DINÂMICA DE AUTENTICAÇÃO NA API
//Como o token recebido na request já é validado pelo middleware FirebaseAuth, nós temos absoluta certeza de que se trata de um usuário dentro de nosso domínio
//Desta forma, nós chamamos o método handleFirebaseAuth que verificará se o usuário local já está injetado no objeto de request
//Caso não esteja, nós entendemos de que se trata de um novo usuário (lembrando, um usuário que faz parte do nosso domínio) e aciona o método de cadastro na API
//Caso esteja, nós prosseguimos com o login na API normalmente.
$obRouter->post('/api/v1/auth', [
    'middlewares' => [],
    function ($request) {
        $response = Api\Auth::handleFirebaseAuth($request);

        return new Response($response['code'], $response, 'application/json');
    }
]);

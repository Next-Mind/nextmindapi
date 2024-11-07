<?php

require __DIR__.'/../vendor/autoload.php';


use \App\Utils\View;
use \App\Http\Middleware\Queue as MiddlewareQueue;
use \WilliamCosta\DotEnv\Environment;
use \WilliamCosta\DatabaseManager\Database;

//DEFINE O FUSO HORÁRIO PARA BRASIL/SAO PAULO
date_default_timezone_set('America/Sao_Paulo');

//CARREGA AS VARIÁVEIS DE AMBIENTE
Environment::load(__DIR__.'/../');

//DEFINE AS CONFIGURAÇÕES DE BANCO DE DADOS
Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_PORT')
);

//DEFINE A CONSTANTE DE URL
define('URL',getenv('URL'));

//DEFINE O VALOR PADRÃO DAS VARIÁVEIS
View::init([
    'URL' => URL
]);

//DEFINE O MAPEAMENTO DE MIDDLEWARES
MiddlewareQueue::setMap([
    'maintenance' => \App\Http\Middleware\Maintenance::class,
    'required-admin-logout' => \App\Http\Middleware\RequireAdminLogout::class,
    'required-admin-login' => \App\Http\Middleware\RequireAdminLogin::class,
    'api' => \App\Http\Middleware\Api::class,
    'user-basic-auth' => \App\Http\Middleware\UserBasicAuth::class,
    'jwt-auth' => \App\Http\Middleware\JWTAuth::class,
    'cache' => \App\Http\Middleware\Cache::class,
    'api-key-auth' => \App\Http\Middleware\ApiKeyAuth::class
    
]);

//DEFINE O MAPEAMENTO DE MIDDLEWARES PADRÕES EXECUTADOS EM TODAS AS ROTAS
MiddlewareQueue::setDefault([
    'maintenance'
]);
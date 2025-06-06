<?php

require __DIR__ . '/../vendor/autoload.php';


use \App\Utils\View;
use \App\Http\Middleware\Queue as MiddlewareQueue;
use \WilliamCosta\DotEnv\Environment;
use \WilliamCosta\DatabaseManager\Database;

//DEFINE O FUSO HORÁRIO PARA BRASIL/SAO PAULO
date_default_timezone_set('America/Sao_Paulo');

//CARREGA AS VARIÁVEIS DE AMBIENTE
Environment::load(__DIR__ . '/../');


//DEFINE AS CONFIGURAÇÕES DE BANCO DE DADOS
Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_PORT')
);

//DEFINE A CONSTANTE DE URL
define('URL', getenv('URL'));

//DEFINE O VALOR PADRÃO DAS VARIÁVEIS
View::init([
    'URL' => URL
]);

//DEFINE O MAPEAMENTO DE MIDDLEWARES
MiddlewareQueue::setMap([
    'allow-cors' => App\Http\Middleware\CorsMiddleware::class,
    'maintenance' => \App\Http\Middleware\Maintenance::class,
    'api' => \App\Http\Middleware\Api::class,
    'jwt-auth' => \App\Http\Middleware\JWTAuth::class,
    'cache' => \App\Http\Middleware\Cache::class,
    'api-key-auth' => \App\Http\Middleware\ApiKeyAuth::class,
    'firebase-auth' => \App\Http\Middleware\FirebaseAuth::class,
    'requires-sign-in' => \App\Http\Middleware\RequiresSignIn::class,
    'requires-psychologist-permission' => \App\Http\Middleware\RequiresPyschoPerm::class,

]);

//DEFINE O MAPEAMENTO DE MIDDLEWARES PADRÕES EXECUTADOS EM TODAS AS ROTAS
MiddlewareQueue::setDefault([
    'allow-cors',
    'maintenance',
    'api',
    'firebase-auth'
]);

//DEFINE O CAMINHO E O NOME DO ARQUIVO COM AS CHAVES PRIVADAS DO FIREBASE
define('FIREBASE_KEY', __DIR__ . '/../' . getenv('FIREBASE_KEY_PATH'));

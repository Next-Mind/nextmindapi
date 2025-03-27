<?php

require __DIR__ . '/../includes/app.php';

use \App\Utils\Logger\Logger;

use \App\Http\Router;

$logger = new Logger('index');

$logger->debug('Iniciando objeto de router');
$obRouter = new Router(URL);

//INCLUI AS ROTAS DA API 
include __DIR__ . '/../routes/api.php';


$logger->debug('Enviando response para o client');
//IMPRIME O RESPONSE DA ROTA
$obRouter->run()->sendResponse();

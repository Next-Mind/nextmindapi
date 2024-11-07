<?php

require dirname(__DIR__,1).'/includes/app.php';

use \App\Http\Router;

$obRouter = new Router(URL);

//INCLUI AS ROTAS DA API 
include dirname(__DIR__,1).'/routes/api.php';

//IMPRIME O RESPONSE DA ROTA
$obRouter->run()->sendResponse();
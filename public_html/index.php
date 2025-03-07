<?php

require __DIR__ . '/../includes/app.php';

use \App\Http\Router;

// Forçar resposta para requisição OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Access-Control-Allow-Credentials: true");
    http_response_code(200);
    exit;
}

$obRouter = new Router(URL);

//INCLUI AS ROTAS DA API 
include __DIR__ . '/../routes/api.php';

//IMPRIME O RESPONSE DA ROTA
$obRouter->run()->sendResponse();

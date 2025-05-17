<?php

//INCLUI AS ROTAS DE AUTENTICAÇÃO DA API
include __DIR__ . '/api/v1/auth.php';

//INCLUI AS ROTAS DEFAULT (V1)
include __DIR__ . '/api/v1/default.php';

//INCLUI AS ROTAS DE LSITA DE CONTATOS DE USUÁRIOS (V1)
include __DIR__ . '/api/v1/userContactList.php';

//INCLUI AS ROTAS DE TIPOS DE USUÁRIOS (V1)
include __DIR__ . '/api/v1/userTypes.php';

//INCLUI AS ROTAS DE USUÁRIOs (V1)
include __DIR__ . '/api/v1/users.php';

//INCLUI AS ROTAS DE CONSULTAS MÉDICAS (V1)
include __DIR__ . '/api/v1/appointments.php';

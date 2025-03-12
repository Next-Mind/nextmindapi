<?php

namespace App\Controller\Api;

use Exception;
use \App\Model\Entity\User as EntityUser;

class Auth extends Api
{

    /**
     * Método responsável por gerenciar a rota de autenticação. É identificado se o usuário já está cadastrado na API e caso não esteja, realiza o cadastro de forma dinâmica.
     *
     * @param  Request $request
     * @return array
     */
    public static function handleFirebaseAuth($request)
    {
        if (!isset($request->user)) {
            return self::setNewFirebaseUser($request);
        } else {
            return self::authFirebaseUser($request);
        }
    }

    /**
     * Método responsável por validar a conexão do usuário via Firebase ID Token
     *
     */
    private static function authFirebaseUser($request)
    {
        $obUser = $request->user;

        $obUser->data_ultimo_acesso = Date('Y-m-d H:i:s');
        $obUser->atualizar();


        return parent::getApiResponse('User authenticated successfully', [
            'user' => [
                'uid' => $obUser->uid,
                'id' => $obUser->id,
                'name' => $obUser->nome,
                'email' => $obUser->email,
                'questionnaire_answered' => (bool) $obUser->questionario_respondido,
                'complete_profile' => (bool) $obUser->perfil_completo,
                'complete_registration' => (bool) $obUser->cadastro_completo
            ]
        ], 200);
    }

    /**
     * Método responsável por cadastrar o usuário na API, com base nos dados recebidos pelo Firebase
     *
     * @param  Request $request
     * @return array
     */
    private static function setNewFirebaseUser($request)
    {
        $user = $request->firebaseUser;
        $uid = $user->uid;

        //REALIZA BUSCA NO BANCO PARA VERIFICAR SE NÃO EXISTE ESTE USUÁRIO
        $obUserUid = EntityUser::getUserByUid($uid);
        if ($obUserUid instanceof EntityUser) {
            throw new Exception("User is already registered", 400);
        }

        //NOVO USUÁRIO
        $obUser = new EntityUser;
        $obUser->uid = $user->uid;
        $obUser->nome = $user->displayName;
        $obUser->email = $user->email;
        $obUser->perfil_completo = 0;
        $obUser->questionario_respondido = 0;
        $obUser->cadastro_completo = 0;
        $obUser->cadastrar();

        $response = [
            'user' => [
                'uid' => $obUser->uid,
                'id' => $obUser->id,
                'name' => $obUser->nome,
                'email' => $obUser->email,
                'questionnaire_answered' => (bool) $obUser->questionario_respondido,
                'complete_profile' => (bool) $obUser->perfil_completo,
                'complete_registration' => (bool) $obUser->cadastro_completo
            ]
        ];

        return parent::getApiResponse('User created successfully', $response, 201);
    }
}

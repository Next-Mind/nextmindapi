<?php

namespace App\Controller\Api;

use Exception;
use \App\Model\Entity\Users\User as EntityUser;

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
        $obUser->update();


        return parent::getApiResponse('User authenticated successfully', [
            'user' => [
                'uid' => $obUser->uid,
                'id' => $obUser->id,
                'name' => $obUser->name,
                'email' => $obUser->email,
                'questionnaire_answered' => (bool) $obUser->questionnaire_answered,
                'complete_profile' => (bool) $obUser->profile_complete,
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
        $obUser->name = $user->displayName;
        $obUser->email = $user->email;
        $obUser->profile_complete = 0;
        $obUser->questionnaire_answered = 0;
        $obUser->register();

        $response = [
            'user' => [
                'uid' => $obUser->uid,
                'id' => $obUser->id,
                'name' => $obUser->name,
                'email' => $obUser->email,
                'questionnaire_answered' => (bool) $obUser->questionnaire_answered,
                'complete_profile' => (bool) $obUser->profile_complete
            ]
        ];

        return parent::getApiResponse('User created successfully', $response, 201);
    }
}

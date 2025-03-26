<?php

namespace App\Controller\Api;

use App\Model\Entity\Users\UserAnswer;
use Exception;

class User extends Api
{

    /**
     * Método responsável por retornar o usuário atualmente conectado
     *
     * @param  Request $request
     * @return array
     */
    public static function getCurrentUser($request)
    {

        return parent::getApiResponse('Success', $request->user);
    }

    /**
     * Método responsável por editar os dados de perfil do usuário atualmente conectado
     *
     * @param  Request $request
     * @return array
     */
    public static function setEditLazyUser($request)
    {
        //CAMPOS OBRIGATÓRIOS
        $requiredFields = ['name', 'birth_date', 'email', 'ra'];

        //VERIFICA SE HÁ OS DADOS DO USUÁRIO
        $postVars = $request->getPostVars();
        $user = $postVars['user'] ?? null;
        if (!$user || array_diff_key(array_flip($requiredFields), $user)) {
            throw new Exception('No required data found', 400);
        }

        //ATUALIZA A INSTÃNCIA ATUAL DE USUÁRIO NO BANCO
        $request->user->data_nascimento = $user['birthday'];
        $request->user->nome = $user['name'];
        $request->user->email = $user['email'];
        $request->user->ra = $user['ra'];
        $request->user->perfil_completo = 1;
        $request->user->atualizar();

        //SUCESSO
        return parent::getApiResponse('User profile has been edited successfully', [
            'user' => [
                'uid' => $request->user->uid,
                'id' => $request->user->id,
                'name' => $request->user->nome,
                'email' => $request->user->email,
                'questionnaire_answered' => (bool) $request->user->questionario_respondido,
                'complete_profile' => (bool) $request->user->perfil_completo,
                'complete_registration' => (bool) $request->user->cadastro_completo
            ]
        ]);
    }

    /**
     * Método responsável por editar os dados do questionário obrigatório do usuário atualmente conectado
     *
     * @param  Request $request
     * @return array
     */
    public static function setEditQuestUser($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //VERIFICA SE HÁ OS DADOS DO QUESTIONÁRIO
        $questionnaire = $postVars['questionnaire'] ?? null;
        if (!$questionnaire) {
            throw new Exception('No required data found', 400);
        }

        if ($request->user->questionario_respondido) {
        }

        //ITERA SOBRE CADA PERGUNTA, SENDO $PERGUNTA => $RESPOSTA, E CADASTRA NO BANCO
        foreach ($questionnaire as $question => $answer) {
            $obUserAnswer = new UserAnswer();
            $obUserAnswer->user_id = $request->user->id;
            $obUserAnswer->question_id = $question;
            $obUserAnswer->answer = $answer;
            $obUserAnswer->register();
        }

        //ATUALIZA O CADASTRO DO USUÁRIO, INFORMANDO QUE O QUESTIONÁRIO FOI RESPONDIDO
        $request->user->questionario_respondido = 1;
        $request->user->atualizar();

        //SUCESSO
        return parent::getApiResponse('The questionnaire was successfully registered', [
            'user' => [
                'uid' => $request->user->uid,
                'id' => $request->user->id,
                'name' => $request->user->name,
                'email' => $request->user->email,
                'questionnaire_answered' => (bool) $request->user->questionnaire_answered,
                'complete_registration' => (bool) $request->user->complete_profile
            ]
        ], 201);
    }

    public static function setEditAddressUser($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //CAMPOS OBRIGATÓRIOS
        $requiredFields = ['address', 'addressNumber', 'zipCode', 'state'];
        $address = $postVars['address'] ?? null;
        if (!$address || array_diff_key(array_flip($requiredFields), $address)) {
            throw new Exception('No required data found', 400);
        }

        if (!is_numeric($address['zipCode'])) {
            throw new Exception('Zip Code must be numeric!');
        }

        //ATUALIZA A INSTÃNCIA ATUAL DE USUÁRIO NO BANCO
        $request->user->logradouro = $address['address'];
        $request->user->numero = $address['addressNumber'];
        $request->user->cep = $address['zipCode'];
        $request->user->estado = $address['state'];
        $request->user->complemento = $address['addressComplement'] ?? '';
        $request->user->atualizar();
        return parent::getApiResponse('User address has been edited successfully', [
            'user' => [
                'uid' => $request->user->uid,
                'id' => $request->user->id,
                'name' => $request->user->nome,
                'email' => $request->user->email,
                'address' => $request->user->logradouro,
                'addressNumber' => $request->user->numero,
                'zipCode' => $request->user->cep,
                'state' => $request->user->estado,
                'addressComplement' => $request->user->complemento,
                'complete_registration' => (bool) $request->user->cadastro_completo
            ]
        ]);
    }

    public static function setEditPersonalUserInfo($request) {}
}

<?php

namespace App\Controller\Api;

use App\Model\Entity\UserAnswer;
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
    public static function setEditProfileUser($request)
    {
        //CAMPOS OBRIGATÓRIOS
        $requiredFields = ['user_type', 'name', 'birthDate', 'email', 'ra'];

        //VERIFICA SE HÁ OS DADOS DO USUÁRIO
        $postVars = $request->getPostVars();
        $user = $postVars['user'] ?? null;
        if (!$user || array_diff_key(array_flip($requiredFields), $user)) {
            throw new Exception('No required data found', 400);
        }

        //ATUALIZA A INSTÃNCIA ATUAL DE USUÁRIO NO BANCO
        $request->user->tipo_usuario_id = $user['user_type'];
        $request->user->data_nascimento = $user['birthDate'];
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
            $obUserAnswer->usuario_id = $request->user->id;
            $obUserAnswer->questao_id = $question;
            $obUserAnswer->resposta = $answer;
            $obUserAnswer->cadastrar();
        }

        //ATUALIZA O CADASTRO DO USUÁRIO, INFORMANDO QUE O QUESTIONÁRIO FOI RESPONDIDO
        $request->user->questionario_respondido = 1;
        $request->user->atualizar();

        //SUCESSO
        return parent::getApiResponse('The questionnaire was successfully registered', [
            'user' => [
                'uid' => $request->user->uid,
                'id' => $request->user->id,
                'name' => $request->user->nome,
                'email' => $request->user->email,
                'questionnaire_answered' => (bool) $request->user->questionario_respondido,
                'complete_profile' => (bool) $request->user->perfil_completo,
                'complete_registration' => (bool) $request->user->cadastro_completo
            ]
        ], 201);
    }
}

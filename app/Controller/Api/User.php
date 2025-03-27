<?php

namespace App\Controller\Api;

use App\Model\Entity\Users\UserAnswer;
use App\Model\Entity\Users\User as EntityUser;
use Kreait\Firebase\Factory;
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

        return parent::getApiResponse('Successfully retrieved user data', ["user" => $request->user]);
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

        //DEFININDO NOME PARA ATUALIZAR NO FIREBASE
        $properties = [
            'displayName' => $user['name']
        ];

        //OBTENDO UID DO USUÁRIO
        $uid = $request->user->uid;

        //INICIANDO INSTÃNCIA DO SDK FIREBASE
        $firebase = (new Factory)
            ->withServiceAccount(FIREBASE_KEY);
        $auth = $firebase->createAuth();

        //ATUALIZANDO O USUÁRIO
        $auth->updateUser($uid, $properties);

        //ATUALIZA A INSTÃNCIA ATUAL DE USUÁRIO NO BANCO
        $request->user->birth_date = $user['birth_date'];
        $request->user->name = $user['name'];
        $request->user->email = $user['email'];
        $request->user->ra = $user['ra'];
        $request->user->updated_at = (new \DateTime())->format('Y-m-d H:i:s');
        $request->user->update();

        //SUCESSO
        return parent::getApiResponse('User profile has been edited successfully', [
            'user' => [
                'uid' => $request->user->uid,
                'id' => $request->user->id,
                'name' => $request->user->name,
                'email' => $request->user->email,
                'questionnaire_answered' => (bool) $request->user->questionnaire_answered,
                'personal_info_complete' => (bool) $request->user->personal_info_complete,
                'address_complete' => (bool) $request->user->address_complete
            ]
        ], 201);
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

        if ($request->user->questionnaire_answered) {
            throw new Exception('Questionnaire already answered', 409);
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
        $request->user->questionnaire_answered = 1;
        $request->user->updated_at = (new \DateTime())->format('Y-m-d H:i:s');
        $request->user->update();

        //SUCESSO
        return parent::getApiResponse('The questionnaire was successfully registered', [
            'user' => [
                'uid' => $request->user->uid,
                'id' => $request->user->id,
                'name' => $request->user->name,
                'email' => $request->user->email,
                'questionnaire_answered' => (bool) $request->user->questionnaire_answered,
                'personal_info_complete' => (bool) $request->user->personal_info_complete,
                'address_complete' => (bool) $request->user->address_complete
            ]
        ], 201);
    }

    /**
     * Método responsável por editar o endereço do usuário atualmente logado
     *
     * @param  Request $request
     * @return array
     */
    public static function setEditAddressUser($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //CAMPOS OBRIGATÓRIOS
        $requiredFields = ['address_street', 'address_number', 'zip_code', 'state'];
        $address = $postVars['address'] ?? null;
        if (!$address || array_diff_key(array_flip($requiredFields), $address)) {
            throw new Exception('No required data found', 400);
        }

        if (!is_numeric($address['zip_code'])) {
            throw new Exception('Zip Code must be numeric!');
        }

        //ATUALIZA A INSTÃNCIA ATUAL DE USUÁRIO NO BANCO
        $request->user->address_street = $address['address_street'];
        $request->user->address_number = $address['address_number'];
        $request->user->zip_code = $address['zip_code'];
        $request->user->state = $address['state'];
        $request->user->address_complement = $address['address_complement'] ?? '';
        $request->user->address_complete = true;
        $request->user->updated_at = (new \DateTime())->format('Y-m-d H:i:s');
        $request->user->update();
        return parent::getApiResponse('User address has been edited successfully', [
            'user' => [
                'uid' => $request->user->uid,
                'id' => $request->user->id,
                'name' => $request->user->name,
                'email' => $request->user->email,
                'address' => $request->user->address_street,
                'address_number' => $request->user->address_number,
                'zip_code' => $request->user->zip_code,
                'state' => $request->user->state,
                'address_complement' => $request->user->address_complement,
                'questionnaire_answered' => (bool) $request->user->questionnaire_answered,
                'personal_info_complete' => (bool) $request->user->personal_info_complete,
                'address_complete' => (bool) $request->user->address_complete
            ]
        ]);
    }

    /**
     * Método responsável por editar as informações pessoais do usuário, voltadas para o uso no agendamento de consultas
     *
     * @param  Request $request
     * @return array
     */
    public static function setEditPersonalUserInfo($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //CAMPOS OBRIGATÓRIOS
        $requiredFields = ['gender', 'cpf', 'phone1'];
        $personal_info = $postVars['personal_info'] ?? null;
        if (!$personal_info || array_diff_key(array_flip($requiredFields), $personal_info)) {
            throw new Exception('No required data found', 400);
        }

        if (!is_numeric($personal_info['cpf']) || !is_numeric($personal_info['phone1']) || !is_numeric($personal_info['phone2'])) {
            throw new Exception("'cpf', 'phone1' or 'phone2' must be numeric!");
        }

        //VERIFICANDO SE NÃO HÁ OUTRO USUÁRIO COM O MESMO CPF
        $obUserCpf = EntityUser::getUserByCpf($personal_info['cpf']);
        if ($obUserCpf instanceof EntityUser) {
            throw new Exception('This CPF is already in use by another person!', 400);
        }

        //ATUALIZA OS DADOS
        $request->user->gender = $personal_info['gender'];
        $request->user->cpf = $personal_info['cpf'];
        $request->user->phone1 = $personal_info['phone1'];
        $request->user->phone2 = $personal_info['phone2'];
        $request->user->personal_info_complete = true;
        $request->user->updated_at = (new \DateTime())->format('Y-m-d H:i:s');
        $request->user->update();
        return parent::getApiResponse('User address has been edited successfully', [
            'user' => [
                'uid' => $request->user->uid,
                'id' => $request->user->id,
                'name' => $request->user->name,
                'email' => $request->user->email,
                'gender' => $request->user->gender,
                'cpf' => $request->user->cpf,
                'phone1' => $request->user->phone1,
                'phone2' => $request->user->phone2,
                'questionnaire_answered' => (bool) $request->user->questionnaire_answered,
                'personal_info_complete' => (bool) $request->user->personal_info_complete,
                'address_complete' => (bool) $request->user->address_complete
            ]
        ]);
    }

    public static function setEditDescriptionProfileInfo($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //CAMPOS OBRIGATÓRIOS
        if (!isset($postVars['description'])) {
            throw new Exception('No required data found', 400);
        }

        $request->user->profile_description = $postVars['description'];
        $request->user->updated_at = (new \DateTime())->format('Y-m-d H:i:s');
        $request->user->update();

        return parent::getApiResponse('User description has been edited successfully', [
            'user' => [
                'uid' => $request->user->uid,
                'id' => $request->user->id,
                'name' => $request->user->name,
                'email' => $request->user->email,
                'profile_description' => $request->user->profile_description,
                'questionnaire_answered' => (bool) $request->user->questionnaire_answered,
                'personal_info_complete' => (bool) $request->user->personal_info_complete,
                'address_complete' => (bool) $request->user->address_complete
            ]
        ]);
    }
}

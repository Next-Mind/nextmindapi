<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class User
{
    /**
     * ID do usuário
     *
     * @var int
     */
    public $id;

    /**
     * UID do usuário (Firebase)
     * 
     * @var string
     */
    public $uid = '';

    /**
     * Tipo do usuário (Aluno, Professor, Psicólogo)
     *
     * @var int
     */
    public $user_type_id = 1;

    /**
     * Nome do usuário
     *
     * @var string
     */
    public $name = '';

    /**
     * E-mail do usuário
     *
     * @var string
     */
    public $email = '';

    /**
     * Data de Nascimento do Usuário
     *
     * @var string
     */
    public $birth_date = '1990-01-01 00:00:00';

    /**
     * CPF do usuário
     *
     * @var string
     */
    public $cpf = '';

    /**
     * RA do usuário, caso seja um aluno
     *
     * @var string
     */
    public $ra = '';

    /**
     * CRP do usuário, caso seja um psicólogo
     *
     * @var string
     */
    public $crp = '';

    /**
     * Telefone principal de contato do usuário
     *
     * @var string
     */
    public $phone1 = '';

    /**
     * Telefone secundário de contato do usuário
     *
     * @var string
     */
    public $phone2 = '';

    /**
     * Logradouro (endereço) do usuário
     *
     * @var string
     */
    public $address_street = '';

    /**
     * Número do endereço do usuário
     *
     * @var string
     */
    public $address_number = '';

    /**
     * Complemento do endereço do usuário
     *
     * @var string
     */
    public $address_complement = '';

    /**
     * CEP do endereço do usuário
     *
     * @var string
     */
    public $zip_code = '';

    /**
     * Estado do endereço do usuário
     *
     * @var string
     */
    public $state = '';

    /**
     * Link para a imagem de perfil do usuário
     *
     * @var string
     */
    public $profile_image = '';

    /**
     * Descrição breve sobre o usuário
     *
     * @var string
     */
    public $profile_description = '';

    /**
     * Situação do usuário (ativo ou inativo)
     *
     * @var int
     */
    public $status = 1;

    /**
     * Data do último acesso do usuário na plataforma
     *
     * @var string
     */
    public $last_access_date = '';

    /**
     * Data de cadastro do usuário no sistema
     *
     * @var string
     */
    public $registration_date = '';

    /**
     * Variável que identifica se o cadastro do usuário está completo ou não
     *
     * @var bool
     */
    public bool $profile_complete = false;

    /**
     * Variável que identifica se o usuário respondeu o formulário obrigatório ou não
     *
     * @var bool
     */
    public bool $questionnaire_answered = false;

    /**
     * Método responsável por cadastrar a instãncia atual de usuário no banco de dados
     *
     * @return boolean
     */
    public function register()
    {
        //INSERE A INSTANCIA ATUAL NO BANCO DE DADOS
        $this->id = (int) (new Database('users'))->insert([
            'uid' => $this->uid,
            'user_type_id' => $this->user_type_id,
            'name' => $this->name,
            'email' => $this->email,
            'birth_date' => $this->birth_date,
            'cpf' => $this->cpf,
            'ra' => $this->ra,
            'crp' => $this->crp,
            'phone1' => $this->phone1,
            'phone2' => $this->phone2,
            'address_street' => $this->address_street,
            'address_number' => $this->address_number,
            'address_complement' => $this->address_complement,
            'zip_code' => $this->zip_code,
            'state' => $this->state,
            'profile_image' => $this->profile_image,
            'profile_description' => $this->profile_description,
            'status' => $this->status,
            'last_access_date' => (new \Datetime())->format('Y-m-d H:i:s'),
            'registration_date' => (new \Datetime())->format('Y-m-d H:i:s'),
            'profile_complete' => (int) $this->profile_complete,
            'questionnaire_answered' => (int) $this->questionnaire_answered,
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar os dados no banco
     *
     * @return boolean
     */
    public function update()
    {
        //INSERE A INSTANCIA ATUAL NO BANCO DE DADOS
        return (new Database('users'))->update('id= ' . $this->id, [
            'uid' => $this->uid,
            'user_type_id' => $this->user_type_id,
            'name' => $this->name,
            'email' => $this->email,
            'birth_date' => $this->birth_date,
            'cpf' => $this->cpf,
            'ra' => $this->ra,
            'crp' => $this->crp,
            'phone1' => $this->phone1,
            'phone2' => $this->phone2,
            'address_street' => $this->address_street,
            'address_number' => $this->address_number,
            'address_complement' => $this->address_complement,
            'zip_code' => $this->zip_code,
            'state' => $this->state,
            'profile_image' => $this->profile_image,
            'profile_description' => $this->profile_description,
            'status' => $this->status,
            'last_access_date' => $this->last_access_date,
            'registration_date' => (new \Datetime())->format('Y-m-d H:i:s'),
            'profile_complete' => (int) $this->profile_complete,
            'questionnaire_answered' => (int) $this->questionnaire_answered,
        ]);
    }

    /**
     * Método responsável por inativar os dados no banco
     *
     * @return boolean
     */
    public function inactivate()
    {
        return (new Database('users'))->update('uid = ' . $this->uid, [
            'status' => 0
        ]);
    }

    /**
     * Método responsável por ativar os dados no banco
     *
     * @return boolean
     */
    public function activate()
    {
        return (new Database('users'))->update('uid = ' . $this->uid, [
            'status' => 1
        ]);
    }

    /**
     * Método responsável por retornar um usuário com base no seu id
     *
     * @param  int $id
     * @return User
     */
    public static function getUserById($id)
    {
        return self::getUsers('id = ' . $id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar um usuário com base no seu uid
     *
     * @param  string $uid
     * @return User
     */
    public static function getUserByUid($uid)
    {
        return self::getUsers('uid = "' . $uid . '"')->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar um usuário com base em seu email
     *
     * @param  string $email
     * @return User
     */
    public static function getUserByEmail($email)
    {
        return self::getUsers('email ="' . $email . '"')->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar Usuários
     *
     * @param  string $where
     * @param  string $order
     * @param  string $limit
     * @param  string $fields
     * @return PDOStatement
     */
    public static function getUsers($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('users'))->select($where, $order, $limit, $fields);
    }
}

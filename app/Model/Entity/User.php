<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

use \JsonSerializable;

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
    public $tipo_usuario_id = 1;

    /**
     * Nome do usuário
     *
     * @var string
     */
    public $nome = '';

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
    public $data_nascimento = '1990-01-01 00:00:00';

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
    public $fone1 = '';

    /**
     * Telefone secundário de contato do usuário
     *
     * @var string
     */
    public $fone2 = '';

    /**
     * Logradouro (endereço) do usuário
     *
     * @var string
     */
    public $logradouro = '';

    /**
     * Número do endereço do usuário
     *
     * @var string
     */
    public $numero = '';

    /**
     * Complemento do endereço do usuário
     *
     * @var string
     */
    public $complemento = '';

    /**
     * CEP do endereço do usuário
     *
     * @var string
     */
    public $cep = '';

    /**
     * Estado do endereço do usuário
     *
     * @var string
     */
    public $estado = '';

    /**
     * Link para a imagem de perfil do usuário
     *
     * @var string
     */
    public $imagem_perfil = '';

    /**
     * Descrição breve sobre o usuário
     *
     * @var string
     */
    public $descricao = '';

    /**
     * Situação do usuário (ativo ou inativo)
     *
     * @var int
     */
    public $situacao = 1;

    /**
     * Data do último acesso do usuário na plataforma
     *
     * @var string
     */
    public $data_ultimo_acesso = '';

    /**
     * Data de cadastro do usuário no sistema
     *
     * @var string
     */
    public $data_cadastro = '';

    /**
     * Variável que identifica se o cadastro do usuário está completo ou não
     *
     * @var bool
     */
    public bool $perfil_completo = false;

    /**
     * Variável que identifica se o usuário respondeu o formulário obrigatório ou não
     *
     * @var bool
     */
    public bool $questionario_respondido = false;

    /**
     * Variável que identifica se o usuário respondeu o formulário obrigatório ou não
     *
     * @var bool
     */
    public bool $cadastro_completo = false;

    /**
     * Método responsável por cadastrar a instãncia atual de usuário no banco de dados
     *
     * @return boolean
     */
    public function cadastrar()
    {
        //INSERE A INSTANCIA ATUAL NO BANCO DE DADOS
        $this->id = (int) (new Database('usuarios'))->insert([
            'uid' => $this->uid,
            'tipo_usuario_id' => $this->tipo_usuario_id,
            'nome' => $this->nome,
            'email' => $this->email,
            'data_nascimento' => $this->data_nascimento,
            'cpf' => $this->cpf,
            'ra' => $this->ra,
            'crp' => $this->crp,
            'fone1' => $this->fone1,
            'fone2' => $this->fone2,
            'logradouro' => $this->logradouro,
            'numero' => $this->numero,
            'complemento' => $this->complemento,
            'cep' => $this->cep,
            'estado' => $this->estado,
            'imagem_perfil' => $this->imagem_perfil,
            'descricao' => $this->descricao,
            'situacao' => $this->situacao,
            'data_ultimo_acesso' => (new \Datetime())->format('Y-m-d H:i:s'),
            'data_cadastro' => (new \Datetime())->format('Y-m-d H:i:s'),
            'perfil_completo' => (int) $this->perfil_completo,
            'questionario_respondido' => (int) $this->questionario_respondido,
            'cadastro_completo' => (int) $this->cadastro_completo
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar os dados no banco
     *
     * @return boolean
     */
    public function atualizar()
    {
        //INSERE A INSTANCIA ATUAL NO BANCO DE DADOS
        return (new Database('usuarios'))->update('id= ' . $this->id, [
            'uid' => $this->uid,
            'tipo_usuario_id' => $this->tipo_usuario_id,
            'nome' => $this->nome,
            'email' => $this->email,
            'data_nascimento' => $this->data_nascimento,
            'cpf' => $this->cpf,
            'ra' => $this->ra,
            'crp' => $this->crp,
            'fone1' => $this->fone1,
            'fone2' => $this->fone2,
            'logradouro' => $this->logradouro,
            'numero' => $this->numero,
            'complemento' => $this->complemento,
            'cep' => $this->cep,
            'estado' => $this->estado,
            'imagem_perfil' => $this->imagem_perfil,
            'descricao' => $this->descricao,
            'situacao' => $this->situacao,
            'data_ultimo_acesso' => $this->data_ultimo_acesso,
            'data_cadastro' => (new \Datetime())->format('Y-m-d H:i:s'),
            'perfil_completo' => (int) $this->perfil_completo,
            'questionario_respondido' => (int) $this->questionario_respondido,
            'cadastro_completo' => (int) $this->cadastro_completo
        ]);
    }

    /**
     * Método responsável por inativar os dados no banco
     *
     * @return boolean
     */
    public function inativar()
    {
        return (new Database('usuarios'))->update('uid = ' . $this->uid, [
            'situacao' => 0
        ]);
    }

    /**
     * Método responsável por ativar os dados no banco
     *
     * @return boolean
     */
    public function ativar()
    {
        return (new Database('usuarios'))->update('uid = ' . $this->uid, [
            'situacao' => 1
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
        return (new Database('usuarios'))->select($where, $order, $limit, $fields);
    }
}

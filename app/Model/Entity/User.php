<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class User {    
    /**
     * ID do usuário
     *
     * @var int
     */
    public $id;    
    /**
     * Tipo do usuário (Aluno, Professor, Psicólogo)
     *
     * @var string
     */
    public $tipo_usuario_id;    
    /**
     * Nome do usuário
     *
     * @var string
     */
    public $nome;    
    /**
     * E-mail do usuário
     *
     * @var string
     */
    public $email;    
    /**
     * Senha do usuário (Criptografada)
     *
     * @var string
     */
    public $senha;    
    /**
     * CPF do usuário
     *
     * @var string
     */
    public $cpf;    
    /**
     * RA do usuário, caso seja um aluno
     *
     * @var string
     */
    public $ra;    
    /**
     * CRP do usuário, caso seja um psicólogo
     *
     * @var string
     */
    public $crp;    
    /**
     * Telefone principal de contato do usuário
     *
     * @var string
     */
    public $fone1;    
    /**
     * Telefone secundário de contato do usuário
     *
     * @var string
     */
    public $fone2;    
    /**
     * Logradouro (endereço) do usuário
     *
     * @var string
     */
    public $logradouro;    
    /**
     * Número do endereço do usuário
     *
     * @var string
     */
    public $numero;    
    /**
     * Complemento do endereço do usuário
     *
     * @var string
     */
    public $complemento;    
    /**
     * CEP do endereço do usuário
     *
     * @var string
     */
    public $cep;    
    /**
     * Estado do endereço do usuário
     *
     * @var string
     */
    public $estado;    
    /**
     * Link para a imagem de perfil do usuário
     *
     * @var string
     */
    public $imagem_perfil;    
    /**
     * Descrição breve sobre o usuário
     *
     * @var string
     */
    public $descricao;    
    /**
     * Situação do usuário (ativo ou inativo)
     *
     * @var boolean
     */
    public $situacao;    
    /**
     * Data do último acesso do usuário na plataforma
     *
     * @var string
     */
    public $data_ultimo_acesso;    
    /**
     * Data de cadastro do usuário no sistema
     *
     * @var string
     */
    public $data_cadastro;
    
    /**
     * Método responsável por cadastrar a instãncia atual de usuário no banco de dados
     *
     * @return boolean
     */
    public function cadastrar(){
        //INSERE A INSTANCIA ATUAL NO BANCO DE DADOS
        $this->id = (new Database('usuarios'))->insert([
            'tipo_usuario_id' => $this->tipo_usuario_id,
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha,
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
            'data_cadastro' => new \DateTime()
        ]);

        //SUCESSO
        return true;
    }
    
    /**
     * Método responsável por atualizar os dados no banco
     *
     * @return boolean
     */
    public function atualizar(){
        //INSERE A INSTANCIA ATUAL NO BANCO DE DADOS
       return (new Database('usuarios'))->update('id= '.$this->id,[
            'tipo_usuario_id' => $this->tipo_usuario_id,
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha,
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
            'data_cadastro' => new \DateTime()
        ]);
    }
    
    /**
     * Método responsável por inativar os dados no banco
     *
     * @return boolen
     */
    public function inativar(){
        return (new Database('usuarios'))->update('id = '.$this->id,[
            'situacao' => 0
        ]);
    }
    
    /**
     * Método responsável por ativar os dados no banco
     *
     * @return boolen
     */
    public function ativar(){
        return (new Database('usuarios'))->update('id = '.$this->id,[
            'situacao' => 1
        ]);
    }
    
    /**
     * Método responsável por retornar um usuário com base no seu id
     *
     * @param  int $id
     * @return User
     */
    public static function getUserById($id){
        return self::getUsers('id = '.$id)->fetchObject(self::class);
    }
    
    /**
     * Método responsável por retornar um usuário com base em seu email
     *
     * @param  string $email
     * @return User
     */
    public static function getUserByEmail($email){
        return self::getUsers('email ="'.$email.'"')->fetchObject(self::class);
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
    public static function getUsers($where = null, $order = null, $limit = null, $fields = '*'){
        return (new Database('usuarios'))->select($where,$order,$limit,$fields);
    }
}
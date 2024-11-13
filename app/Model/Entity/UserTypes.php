<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class UserTypes {    
    /**
     * ID do registro
     *
     * @var integer
     */
    public $id;    
    /**
     * Nome do tipo de usuário
     *
     * @var string
     */
    public $nome;    
    /**
     * Descrição do tipo de usuário
     *
     * @var string
     */
    public $descricao;
    
    /**
     * Situação (1 - ativo | 0 - inativo) do registro
     *
     * @var boolean
     */
    public $situacao;
    
    /**
     * Cadastra a instância atual no banco de dados
     *
     * @return boolean
     */
    public function cadastrar(){
        $this->id = (new Database('tipos_usuarios'))->insert([
            'nome' => $this->nome,
            'descricao' => $this->descricao
        ]);

        //SUCESSO
        return true;
    }
    
    /**
     * Atualiza o registro no banco com a instância atual
     *
     * @return boolean
     */
    public function atualizar(){
        return (new Database('tipos_usuarios'))->update('id = '.$this->id,[
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'situacao' => $this->situacao
        ]);
    }
    
    /**
     * Inativa o registro no banco com a instância atual
     *
     * @return boolean
     */
    public function inativar(){
        return (new Database('tipos_usuarios'))->update('id = '.$this->id,[
            'situacao' => $this->situacao
        ]);
    }
    
    /**
     * Método responsável por retornar um tipo de usuário baseado no seu ID
     *
     * @param  integer $id
     * @return UserTypes
     */
    public static function getUserTypeById($id) {
        return self::getUserTypes('id = '.$id)->fetchObject(self::class);
    }

    public static function getUserTypeByName($name){
        return self::getUserTypes('nome = "'.$name.'"')->fetchObject(self::class);

    }
    
    /**
     * Método responsável por retornar tipos de usuários
     *
     * @param  string $where
     * @param  string $order
     * @param  string $limit
     * @param  string $fields
     * @return PDOStatement
     */
    public static function getUserTypes($where = null, $order = null, $limit = null, $fields = '*'){
        return (new Database('tipos_usuarios'))->select($where,$order,$limit,$fields);
    }
}
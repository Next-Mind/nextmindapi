<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class UserContactList {

    /**
     * ID do usuário dono da lista de contatos
     *
     * @var integer
     */
    public $usuario_id;   

    /**
     * ID do usuário que foi adicionado à lista de contatos
     *
     * @var integer
     */
    public $contato_id; 

    /**
     * Apelido do usuário na lista de contatos
     *
     * @var string
     */
    public $apelido;
    
    /**
     * Data em que foi realziado a adição
     *
     * @var string
     */
    public $data_adicao;
    
    /**
     * Método responsável por cadastrar a instância atual no banco de dados
     *
     * @return boolean
     */
    public function cadastrar(){
        (new Database('lista_contatos_usuarios'))->insert([
            'usuario_id' => $this->usuario_id,
            'contato_id' => $this->contato_id,
            'apelido'    => $this->apelido,
            'data_adicao' => Date('Y-m-d H:i:s')
        ]);

        //SUCESSO
        return true;
    }
    
    /**
     * Método responsável por atualizar o contato no banco
     *
     * @return boolean
     */
    public function atualizar(){
        return (new Database('lista_contatos_usuarios'))->update("usuario_id = ".$this->usuario_id." AND contato_id = ".$this->contato_id,[
            'usuario_id' => $this->usuario_id,
            'contato_id' => $this->contato_id,
            'apelido'    => $this->apelido,                                                          
            'data_adicao' => Date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Método responsável por inativar os dados no banco
     *
     * @return boolean
     */
    public function deletar(){
        return (new Database('lista_contatos_usuarios'))->delete("usuario_id = ".$this->usuario_id." AND contato_id = ".$this->contato_id);
    }

    public static function getContactByUserId($userId){
        return self::getContacts('usuario_id = '.$userId);
    }

    public static function isUserInContactList($user_id,$contact_id) {
        return self::getContacts("usuario_id = ".$user_id." AND contato_id = ".$contact_id)->fetchObject(self::class);
    }
    
    /**
     * Método responsável por retornar Contatos
     *
     * @param  string $where
     * @param  string $order
     * @param  string $limit
     * @param  string $fields
     * @return PDOStatement
     */
    public static function getContacts($where = null, $order = null, $limit = null, $fields = '*'){
        return (new Database('lista_contatos_usuarios'))->select($where,$order,$limit,$fields);
    }
    
}

<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class UserContactList
{

    /**
     * ID do usuário dono da lista de contatos
     *
     * @var int
     */
    public int $user_id;

    /**
     * ID do usuário que foi adicionado à lista de contatos
     *
     * @var int
     */
    public $contact_id;

    /**
     * Apelido do usuário na lista de contatos
     *
     * @var string
     */
    public string $nickname;

    /**
     * Data em que foi realizado a adição
     *
     * @var string
     */
    public string $created_at;

    /**
     * Data em que foi atualizado o registro
     *
     * @var string
     */
    public string $updated_at;

    /**
     * Método responsável por cadastrar a instância atual no banco de dados
     *
     * @return bool
     */
    public function register()
    {
        (new Database('users_contact_list'))->insert([
            'user_id'    => $this->user_id,
            'contact_id' => $this->contact_id,
            'nickname'   => $this->nickname,
            'created_at' => Date('Y-m-d H:i:s'),
            'updated_at' => Date('Y-m-d H:i:s')
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar o contato no banco
     *
     * @return bool
     */
    public function update()
    {
        return (new Database('users_contact_list'))->update("user_id = " . $this->user_id . " AND contact_id = " . $this->contact_id, [
            'user_id'    => $this->user_id,
            'contact_id' => $this->contact_id,
            'nickname'   => $this->nickname,
            'created_at' => $this->created_at,
            'updated_at' => Date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Método responsável por inativar os dados no banco
     *
     * @return bool
     */
    public function deletar()
    {
        return (new Database('users_contact_list'))->delete("user_id = " . $this->user_id . " AND contact_id = " . $this->contact_id);
    }

    /**
     * Método responsável por retornar o contato com base no ID do usuário atualmente logado
     *
     * @param  string $userId
     * @return void
     */
    public static function getContactByUserId($userId)
    {
        return self::getContacts('user_id = ' . $userId);
    }

    public static function isUserInContactList($user_id, $contact_id)
    {
        return self::getContacts("user_id = " . $user_id . " AND contact_id = " . $contact_id)->fetchObject(self::class);
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
    public static function getContacts($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('users_contact_list'))->select($where, $order, $limit, $fields);
    }
}

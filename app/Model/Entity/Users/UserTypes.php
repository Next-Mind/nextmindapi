<?php

namespace App\Model\Entity\Users;

use \WilliamCosta\DatabaseManager\Database;

class UserTypes
{
    /**
     * ID do registro
     *
     * @var integer
     */
    public int $id;
    /**
     * Nome do tipo de usuário
     *
     * @var string
     */
    public string $name;
    /**
     * Descrição do tipo de usuário
     *
     * @var string
     */
    public string $description;

    /**
     * Situação (1 - ativo | 0 - inativo) do registro
     *
     * @var bool
     */
    public bool $status;

    /**
     * Cadastra a instância atual no banco de dados
     *
     * @return bool
     */
    public function register()
    {
        $this->id = (new Database('user_types'))->insert([
            'name' => $this->name,
            'description' => $this->description
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Atualiza o registro no banco com a instância atual
     *
     * @return bool
     */
    public function update()
    {
        return (new Database('user_types'))->update('id = ' . $this->id, [
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status
        ]);
    }

    /**
     * Inativa o registro no banco com a instância atual
     *
     * @return bool
     */
    public function inactivate()
    {
        return (new Database('user_types'))->update('id = ' . $this->id, [
            'status' => $this->status
        ]);
    }

    /**
     * Método responsável por retornar um tipo de usuário baseado no seu ID
     *
     * @param  integer $id
     * @return UserTypes
     */
    public static function getUserTypeById($id)
    {
        return self::getUserTypes('id = ' . $id)->fetchObject(self::class);
    }

    public static function getUserTypeByName($name)
    {
        return self::getUserTypes('name = "' . $name . '"')->fetchObject(self::class);
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
    public static function getUserTypes($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('user_types'))->select($where, $order, $limit, $fields);
    }
}

<?php

namespace App\Model\Entity\Users;

use \WilliamCosta\DatabaseManager\Database;

class UserLoginHistory
{
    /**
     * ID da tentativa de login
     *
     * @var int
     */
    public int $id;

    /**
     * ID do usuário
     *
     * @var int
     */
    public int $user_id;

    /**
     * Data em que foi realizado o login
     *
     * @var string
     */
    public string $date;

    /**
     * Registra a instãncia atual no banco de dados
     *
     * @return bool
     */
    public function register()
    {
        $this->id = (new Database('users_login_history'))->insert([
            'user_id' => $this->user_id,
            'date' => (new \Datetime())->format('Y-m-d H:i:s')
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por retornar o histórico de login de um usuário
     *
     * @param  int $user_id
     * @return UserLoginHistory
     */
    public static function getLoginHistoryByUserId($user_id)
    {
        return self::getLoginHistory('user_id = ' . $user_id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar históricos de login
     *
     * @param  string $where
     * @param  string $order
     * @param  string $limit
     * @param  string $fields
     * @return PDOStatement
     */
    public static function getLoginHistory($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('users_login_history'))->select($where, $order, $limit, $fields);
    }
}

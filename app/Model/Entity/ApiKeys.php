<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;

class ApiKeys
{
    /**
     * ID da Api Key
     *
     * @var integer
     */
    public $id;

    /**
     * Plataforma da API Key
     *
     * @var string
     */
    public $platform;

    /**
     * Hash da API Key
     *
     * @var string
     */
    public $hash_api_key;

    /**
     * Situação da API Key (1 - Ativa | 0 - Inativa)
     *
     * @var string
     */
    public $status;

    public static function getApiKeyByPlatform($platform)
    {
        return self::getApiKeys('platfrom = "' . $platform . '"')->fetchObject(self::class);
    }

    private static function getApiKeys($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('api_keys'))->select($where, $order, $limit, $fields);
    }
}

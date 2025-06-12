<?php

namespace App\Model\Entity\Appointments;

use \WilliamCosta\DatabaseManager\Database;

/**
 * Model de Disponibilidade do Psicólogo
 */
class PsychoAvailabilities
{

    const STATUS_CANCELLED = 0;
    const STATUS_AVAILABLE = 1;
    const STATUS_PRE_RESERVED = 2;
    const STATUS_RESERVED = 3;


    /**
     * @var int
     * ID no Banco de Dados da Disponibilidade
     */
    public int $id;

    /**
     * @var int
     * ID do Psicólogo vinculado ao registro
     */
    public int $psychologist_id;

    /**
     * @var string
     * Data de início da disponibilidade
     */
    public string $date;

    /**
     * @var int
     * Status da disponibilidade
     */
    public int $status;

    /**
     * Data em que a disponibilidade foi criada
     *
     * @var mixed
     */
    public $created_at;

    /**
     * Data em que a disponibilidade foi atualizada
     *
     * @var mixed
     */
    public $updated_at;

    /**
     * @return int
     * Método responsável por cadastrar uma nova disponibilidade
     */
    public function register()
    {
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');

        return $this->id = (new Database('psycho_availabilities'))->insert([
            'psychologist_id' => $this->psychologist_id,
            'date' => $this->date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);
    }

    /**
     * Método responsável por atualizar a disponibilidade
     * @return boolean
     */
    public function update()
    {
        $this->updated_at = date('Y-m-d H:i:s');

        return (new Database('psycho_availabilities'))->update('id = ' . $this->id, [
            'psychologist_id' => $this->psychologist_id,
            'date' => $this->date,
            'status' => $this->status,
            'updated_at' => $this->updated_at
        ]);
    }

    /**
     * Método responsável por deletar a disponibilidade
     * @return boolean
     */
    public function delete()
    {
        return (new Database('psycho_availabilities'))->delete('id = ' . $this->id);
    }

    /**
     * Método responsável por retornar as disponibilidades
     *
     * @param  string $where
     * @param  string $order
     * @param  int $limit
     * @param  string $fields
     * @return PDOStatement
     */
    public static function getPsychoAvailabilities($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('psycho_availabilities'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por retornar uma disponibilidade com base no seu id
     *
     * @param  int $id
     * @return PsychoAvailabilities
     */
    public static function getPsychoAvailabilitiesById($id)
    {
        return self::getPsychoAvailabilities('id = ' . $id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar uma disponibilidade com base no seu id do psicólogo
     *
     * @param  int $id
     * @return PsychoAvailabilities
     */
    public static function getPsychoAvailabilitiesByPsychoId($id)
    {
        return self::getPsychoAvailabilities('psychologist_id = ' . $id);
    }

    /**
     * Método responsável por retornar uma disponibilidade com base no seu id do psicólogo e a data
     *
     * @param  int $id
     * @param  string $date
     * @return PsychoAvailabilities
     */
    public static function getPsychoAvailabilitiesByPsychoIdAndDate($id, $date)
    {
        return self::getPsychoAvailabilities('psychologist_id = ' . $id . ' AND date = "' . $date . '"')->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar uma disponibilidade com base no seu id do psicólogo e o intervalo de datas
     *
     * @param  int $id
     * @param  string $startDate
     * @param  string $endDate
     * @return PDOStatement
     */
    public static function getPsychoAvailabilitiesByPsychoIdAndDateRange($id, $startDate, $endDate)
    {
        return self::getPsychoAvailabilities('psychologist_id = ' . $id . ' AND date BETWEEN "' . $startDate . '" AND "' . $endDate . '"' . " AND status = '1'");
    }

    /**
     * Método responsável por retornar uma disponibilidade com base no seu id do psicólogo e o id da disponibilidade
     *
     * @param  int $id
     * @param  int $availabilityId
     * @return PsychoAvailabilities
     */
    public static function getPsychoAvailabilityByPsychoIdAndId($id, $availabilityId)
    {
        return self::getPsychoAvailabilities('psychologist_id = ' . $id . ' AND id = ' . $availabilityId)->fetchObject(self::class);
    }
}

<?php

namespace App\Model\Entity\Appointments;

use \WilliamCosta\DatabaseManager\Database;

/**
 * Model de Consulta Agendada
 */
class PsychoAppointments
{
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no_show';

    public $id;
    public $availability_id;
    public $user_id;
    public $description;
    public $resolution;
    public $notes_private;
    public $attachment_path;
    public $session_link;
    public $status = 'scheduled';
    public $cancelled_by;
    public $cancel_reason;
    public $duration_minutes;
    public $rating;
    public $reminder_sent = false;
    public $created_at;
    public $updated_at;
    public $psychologist_name;
    public $appointment_datetime;


    /**
     * Método responsável por cadastrar uma nova consulta
     *
     * @return int
     */
    public function register()
    {
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');

        return $this->id = (new Database('psycho_appointments'))->insert([
            'availability_id'   => $this->availability_id,
            'user_id'           => $this->user_id,
            'description'       => $this->description,
            'resolution'        => $this->resolution,
            'notes_private'     => $this->notes_private,
            'attachment_path'   => $this->attachment_path,
            'session_link'      => $this->session_link,
            'status'            => $this->status,
            'cancelled_by'      => $this->cancelled_by,
            'cancel_reason'     => $this->cancel_reason,
            'duration_minutes'  => $this->duration_minutes,
            'rating'            => $this->rating,
            'reminder_sent'     => $this->reminder_sent,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at
        ]);
    }

    /**
     * Método responsável por atualizar os dados de uma consulta
     *
     * @return bool
     */
    public function update()
    {
        $this->updated_at = date('Y-m-d H:i:s');

        return (new Database('psycho_appointments'))->update('id = ' . $this->id, [
            'availability_id'   => $this->availability_id,
            'user_id'           => $this->user_id,
            'description'       => $this->description,
            'resolution'        => $this->resolution,
            'notes_private'     => $this->notes_private,
            'attachment_path'   => $this->attachment_path,
            'session_link'      => $this->session_link,
            'status'            => $this->status,
            'cancelled_by'      => $this->cancelled_by,
            'cancel_reason'     => $this->cancel_reason,
            'duration_minutes'  => $this->duration_minutes,
            'rating'            => $this->rating,
            'reminder_sent'     => $this->reminder_sent,
            'updated_at'        => $this->updated_at
        ]);
    }

    /**
     * Método responsável por deletar uma consulta
     *
     * @return boolean
     */
    public function delete()
    {
        return (new Database('psycho_appointments'))->delete('id = ' . $this->id);
    }

    /**
     * Método responsável por retornar uma consulta com base no ID
     *
     * @param  int $id
     * @return PsychoAppointments
     */
    public static function getAppointmentById($id)
    {
        return self::getAppointments('id = ' . $id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar todas as consultas de um usuário
     *
     * @param  int $userId
     * @return PDOStatement
     */
    public static function getAppointmentsByUserId($userId, $limit = null)
    {

        $limit = strlen($limit) ? 'LIMIT ' . $limit : '';
        $sql = "
            SELECT 
                pa.*, 
                u_psy.name AS psychologist_name,
                av.date AS appointment_datetime
            FROM psycho_appointments pa
            JOIN psycho_availabilities av ON pa.availability_id = av.id
            JOIN users u_psy ON av.psychologist_id = u_psy.id
            WHERE pa.user_id = ?
            ORDER BY av.date
        " . $limit;
        return (new Database())->execute($sql, [$userId]);
    }

    /**
     * Método responsável por retornar todas as consultas de uma disponibilidade
     *
     * @param  int $availabilityId
     * @return PDOStatement
     */
    public static function getAppointmentsByAvailabilityId($availabilityId)
    {
        return self::getAppointments('availability_id = ' . $availabilityId);
    }

    /**
     * Método responsável por retornar todas as consultas de um psicólogo
     *
     * @param  int $psychologistId
     * @return PDOStatement
     */
    public static function getAppointmentsByPsychologistId($psychologistId)
    {
        $sql = "
            SELECT 
                pa.*, 
                u.name as user_name, 
                av.date, av.time
            FROM psycho_appointments pa
            JOIN psycho_availabilities av ON pa.availability_id = av.id
            JOIN users u ON pa.user_id = u.id
            WHERE av.psychologist_id = ?
            ORDER BY av.date, av.time
        ";

        return (new Database())->execute($sql, [$psychologistId]);
    }

    /**
     * Retorna consultas com base no filtro
     */
    public static function getAppointments($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('psycho_appointments'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por verificar se o usuário já possui uma consulta agendada para o horário
     *
     * @param  int $userId
     * @param  string $datetime
     * @return bool
     */
    public static function userHasAppointmentAtDatetime($userId, $datetime)
    {
        $sql = "
            SELECT pa.id
            FROM psycho_appointments pa
            JOIN psycho_availabilities av ON pa.availability_id = av.id
            WHERE pa.user_id = ? AND av.date = ?
            LIMIT 1
        ";

        return (new Database())
            ->execute($sql, [$userId, $datetime])
            ->fetchObject() ? true : false;
    }

    /**
     * Retorna os dados parciais da consulta
     *
     * @return array
     */
    public function getPartialData()
    {
        return [
            'id'                => $this->id,
            'availability_id'   => $this->availability_id,
            'user_id'           => $this->user_id,
            'description'       => $this->description,
            'status'            => $this->status,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at
        ];
    }
}

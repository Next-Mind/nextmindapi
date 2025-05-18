<?php

namespace App\Model\Entity\Appointments;

use \WilliamCosta\DatabaseManager\Database;

/**
 * Model de Consulta Agendada
 */
class PsychoAppointments
{
    public int $id;
    public int $availability_id;
    public int $user_id;
    public string $description;
    public string $resolution;
    public string $notes_private;
    public string $attachment_path;
    public string $session_link;
    public string $status = 'scheduled';
    public string $cancelled_by;
    public string $cancel_reason;
    public int $duration_minutes;
    public int $rating;
    public bool $reminder_sent = false;
    public $created_at;
    public $updated_at;


    /**
     * Método responsável por cadastrar uma nova consulta
     *
     * @return int
     */
    public function register()
    {
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
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Método responsável por atualizar os dados de uma consulta
     *
     * @return bool
     */
    public function update()
    {
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
            'updated_at'        => date('Y-m-d H:i:s')
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
    public static function getAppointmentsByUserId($userId)
    {
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
    ";
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
        SELECT pa.*, u.name as user_name, av.date, av.time
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
}

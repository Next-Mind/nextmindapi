<?php

namespace App\Controller\Api\Appointments;

use App\Controller\Api\Api;
use App\Model\Entity\Appointments\PsychoAvailabilities;
use App\Model\Entity\Users\User;

class Appointments extends Api
{
    /**
     * Método responsável por criar uma nova consulta no sistema
     *
     * @param  Request $request
     * @return void
     */
    public static function setNewAppointment($request) {}

    /**
     * Método responsável por listar as consultas de um usuário
     *
     * @param  Request $request
     * @return void
     */
    public static function listAppointmentsByUser($request) {}

    /**
     * Método responsável por listar as consultas de um psicólogo
     *
     * @param  mixed $request
     * @return void
     */
    public static function listAppointmentsByPsychologist($request) {}

    /**
     * Método responsável por cancelar uma consulta
     *
     * @param  mixed $request
     * @return void
     */
    public static function cancelAppointment($request) {}

    /**
     * Método responsável por atualizar o status de uma consulta (Concluído ou não compareceu)
     *
     * @param  mixed $request
     * @return void
     */
    public static function updateStatusAppointment($request) {}

    /**
     * Método responsável por avaliar uma consulta
     *
     * @param  mixed $request
     * @return void
     */
    public static function rateAppointment($request) {}

    /**
     * Método responsável por adicionar um anexo a uma consulta
     *
     * @param  mixed $request
     * @return void
     */
    public static function uploadAttachmentToAppointment($request) {}

    /**
     * Método responsável por adicionar uma resolução a uma consulta
     *
     * @param  mixed $request
     * @return void
     */
    public static function addResolutionToAppointment($request) {}

    /**
     * Método responsável por marcar o lembrete de uma consulta como enviado
     *
     * @param  Request $request
     * @return void
     */
    public static function markReminderSent($request) {}

    /**
     * Retorna os detalhes de uma consulta na visão de um psicólogo (retorna notas privadas)
     *
     * @return void
     */
    public static function getAppointmentDetailsByPsycho() {}

    /**
     * Retorna os detalhes de uma consulta na visão de um usuário (não retorna notas privadas)
     *
     * @return void
     */
    public static function getAppointmentDetailsByUser() {}

    /**
     * Método responsável por reprogramar uma consulta
     *
     * @param  mixed $request
     * @return void
     */
    public static function rescheduleAppointment($request) {}

    /**
     * Método responsável por retornar as estatísticas de consultas
     *
     * @param  mixed $request
     * @return void
     */
    public static function getAppointmentStats($request) {}
}

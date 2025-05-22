<?php

namespace App\Controller\Api\Appointments;

use App\Controller\Api\Api;
use App\Model\Entity\Appointments\PsychoAvailabilities;
use App\Model\Entity\Appointments\PsychoAppointments;
use App\Model\Entity\Users\User;

class Appointments extends Api
{
    /**
     * Método responsável por criar uma nova consulta no sistema
     *
     * @param  Request $request
     * @return void
     */
    public static function setNewAppointment($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //RECUPERA A DISPONIBILIDADE DE HORÁRIO QUE SERÁ AMARRADA A CONSULTA
        $availabilityId = $postVars["availability_id"];

        //ID DO USUÁRIO QUE ESTÁ MARCANDO A CONSULTA
        $userId = $request->user->id;

        //VERIFICA O ID DA DISPONIBILIDADE É VÁLIDO
        if (empty($availabilityId) || !is_numeric($availabilityId)) {
            return parent::getApiResponse('Error processing the request', [
                'Invalid availability ID'
            ], 400);
        }

        //VERIFICA SE A DISPONIBILIDADE DE HORÁRIO EXISTE
        $availability = PsychoAvailabilities::getPsychoAvailabilitiesById($availabilityId);
        if (!$availability instanceof PsychoAvailabilities) {
            return parent::getApiResponse('Error processing the request', [
                'Availability not found'
            ], 400);
        }

        //VERIFICA SE A DISPONIBILIDADE NÃO ESTÁ OCUPADA
        if ($availability->status != 0) {
            return parent::getApiResponse('Error processing the request', [
                'Availability is not available for booking'
            ], 400);
        }

        //VERIFICA SE NÃO É UMA DISPONIBILIDADE ANTIGA
        if (strtotime($availability->date) <= time()) {
            return parent::getApiResponse('Error processing the request', [
                'Cannot schedule an appointment in the past'
            ], 400);
        }

        //VERIFICA SE NÃO HÁ UMA CONSULTA JÁ MARCADA PARA O HORÁRIO
        $appointment = PsychoAppointments::getAppointmentsByAvailabilityId($availabilityId)->fetchObject(PsychoAppointments::class);
        if ($appointment instanceof PsychoAppointments) {
            return parent::getApiResponse('Error processing the request', [
                'Appointment already scheduled for this time'
            ], 400);
        }

        //VERIFICA SE O USUÁRIO NÃO MARCOU UMA CONSULTA PARA O HORÁRIO
        $conflictingAppointment = PsychoAppointments::userHasAppointmentAtDatetime($userId, $availability->date);
        if ($conflictingAppointment) {
            return parent::getApiResponse('Error processing the request', [
                'User already has an appointment scheduled for this time'
            ], 400);
        }

        $appointment = new PsychoAppointments();
        $appointment->availability_id = $availabilityId;
        $appointment->user_id = $userId;
        $appointment->description = $postVars["description"] ?? null;
        $appointment->register();

        return parent::getApiResponse('Appointment successfully scheduled', [
            'appointment' => $appointment->getPartialData()
        ]);
    }

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

<?php

namespace App\Controller\Api\Appointments;

use App\Controller\Api\Api;
use App\Model\Entity\Appointments\PsychoAvailabilities;
use App\Model\Entity\Appointments\PsychoAppointments;
use App\Model\Entity\Users\UserTypes;
use App\Utils\Logger\Logger;
use WilliamCosta\DatabaseManager\Pagination;


class Appointments extends Api
{

    private static $logger = null;

    protected static function getLogger()
    {
        if (self::$logger === null) {
            self::$logger = new Logger('appointmentsController');
        }
        return self::$logger;
    }

    /**
     * Método responsável por criar uma nova consulta no sistema
     *
     * @param  Request $request
     * @return array
     */
    public static function setNewAppointment($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //RECUPERA A DISPONIBILIDADE DE HORÁRIO QUE SERÁ AMARRADA A CONSULTA
        $availabilityId = $postVars["availability_id"];

        //ID DO USUÁRIO QUE ESTÁ MARCANDO A CONSULTA
        $userId = $request->user->id;

        self::getLogger()->debug('Validando ID da Disponibilidade: ' . $availabilityId);
        //VERIFICA O ID DA DISPONIBILIDADE É VÁLIDO
        if (empty($availabilityId) || !is_numeric($availabilityId)) {
            return parent::getApiResponse('Error processing the request', [
                'Invalid availability ID'
            ], 400);
        }

        self::getLogger()->debug('Validando se o horário existe');
        //VERIFICA SE A DISPONIBILIDADE DE HORÁRIO EXISTE
        $availability = PsychoAvailabilities::getPsychoAvailabilitiesById($availabilityId);
        if (!$availability instanceof PsychoAvailabilities) {
            return parent::getApiResponse('Error processing the request', [
                'Availability not found'
            ], 400, self::REQUEST_ERROR);
        }

        self::getLogger()->debug('Validando Status da Disponibilidade: ' . $availability->status);
        //VERIFICA SE A DISPONIBILIDADE NÃO ESTÁ OCUPADA
        if ($availability->status != 2) {
            return parent::getApiResponse('Error processing the request', [
                'Availability is not available for booking'
            ], 400, self::REQUEST_ERROR);
        }

        self::getLogger()->debug('Validando se não é disponibilidade antiga: ');
        //VERIFICA SE NÃO É UMA DISPONIBILIDADE ANTIGA
        if (strtotime($availability->date) <= time()) {
            return parent::getApiResponse('Error processing the request', [
                'Cannot schedule an appointment in the past'
            ], 400, self::REQUEST_ERROR);
        }

        self::getLogger()->debug('Validando se não há consulta marcada para o horário: ');
        //VERIFICA SE NÃO HÁ UMA CONSULTA JÁ MARCADA PARA O HORÁRIO
        $appointment = PsychoAppointments::getAppointmentsByAvailabilityId($availabilityId)->fetchObject(PsychoAppointments::class);
        if ($appointment instanceof PsychoAppointments) {
            return parent::getApiResponse('Error processing the request', [
                'Appointment already scheduled for this time'
            ], 400, self::REQUEST_ERROR);
        }

        self::getLogger()->debug('Validando se o usuário não tem consulta marcada para o mesmo horário: ');
        //VERIFICA SE O USUÁRIO NÃO MARCOU UMA CONSULTA PARA O HORÁRIO
        $conflictingAppointment = PsychoAppointments::userHasAppointmentAtDatetime($userId, $availability->date);
        if ($conflictingAppointment) {
            return parent::getApiResponse('Error processing the request', [
                'User already has an appointment scheduled for this time'
            ], 400, self::REQUEST_ERROR);
        }

        self::getLogger()->debug('Criando a consulta: ');
        //CRIA A CONSULTA
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
    public static function listAppointmentsByUser($request)
    {
        return parent::getApiResponse('Successful return of the list of appointments', [
            'appointments' => self::listAppointmentsByUserItems($request, $obPagination),
            'pagination'   => parent::getPagination($request, $obPagination)
        ]);
    }

    /**
     * Método responsável por listar os itens de consulta de um usuário
     *
     * @param  Request $request
     * @param  Pagination $obPagination
     * @return array
     */
    private static function listAppointmentsByUserItems($request, &$obPagination)
    {
        $items = [];

        //QUANTIDADE TOTAL DE REGISTROS
        $totalLength = PsychoAppointments::getAppointments('user_id = ' . $request->user->id, null, null, 'COUNT(*) as total')->fetchObject()->total;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //QUANTIDADE POR PÁGINA
        $qtdPagina = $queryParams['results'] ?? 5;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($totalLength, $paginaAtual, $qtdPagina);

        //RESULTADOS DA PÁGINA
        $results = PsychoAppointments::getAppointmentsByUserId($request->user->id, $obPagination->getLimit());

        while ($obAppointment = $results->fetchObject(PsychoAppointments::class)) {
            $items[] = [
                'id' => $obAppointment->id,
                'availability_id' => $obAppointment->availability_id,
                'pyschologist_name' => $obAppointment->psychologist_name,
                'appointment_datetime' => $obAppointment->appointment_datetime,
                'status' => $obAppointment->status,
                'created_at' => $obAppointment->created_at,
                'updated_at' => $obAppointment->updated_at
            ];
        }

        return $items;
    }

    /**
     * Método responsável por renderizar cada consulta de um psicólogo
     *
     * @param  Request $request
     * @param  Pagination $obPagination
     * @return array
     */
    private static function listAppointmentsByPsychologistItems($request, &$obPagination)
    {
        $items = [];

        //QUANTIDADE TOTAL DE REGISTROS
        $totalLength = PsychoAppointments::getAppointments('psychologist_id = ' . $request->user->id, null, null, 'COUNT(*) as total')->fetchObject()->total;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //QUANTIDADE POR PÁGINA
        $qtdPagina = $queryParams['results'] ?? 5;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($totalLength, $paginaAtual, $qtdPagina);

        //RESULTADOS DA PÁGINA
        $results = PsychoAppointments::getAppointmentsByPsychologistId($request->user->id, $obPagination->getLimit());

        while ($obAppointment = $results->fetchObject(PsychoAppointments::class)) {
            $items[] = [
                'id' => $obAppointment->id,
                'availability_id' => $obAppointment->availability_id,
                'pyschologist_name' => $obAppointment->psychologist_name,
                'appointment_datetime' => $obAppointment->appointment_datetime,
                'status' => $obAppointment->status,
                'created_at' => $obAppointment->created_at,
                'updated_at' => $obAppointment->updated_at
            ];
        }

        return $items;
    }

    /**
     * Método responsável por listar as consultas de um psicólogo
     *
     * @param  Request $request
     * @return void
     */
    public static function listAppointmentsByPsychologist($request)
    {
        return parent::getApiResponse('Successful return of the list of appointments', [
            'appointments' => self::listAppointmentsByPsychologistItems($request, $obPagination),
            'pagination'   => parent::getPagination($request, $obPagination)
        ]);
    }

    /**
     * Método responsável por cancelar uma consulta
     *
     * @param  mixed $request
     * @return void
     */
    public static function cancelAppointment($request)
    {
        //ID DO USUÁRIO QUE ESTÁ REQUISITANDO O CANCELAMENTO
        $userId = $request->user->id;
        $userType = $request->user->user_type_id;

        //POST VARS
        $postVars = $request->getPostVars();

        //ID DA CONSULTA A SER CANCELADA
        $appointmentId = $postVars["appointment_id"] ?? null;

        //VALIDA SE O ID DA CONSULTA É VÁLIDO
        if (empty($appointmentId) || !is_numeric($appointmentId)) {
            return parent::getApiResponse('Error processing the request', [
                'Invalid appointment ID'
            ], 400);
        }

        //RECUPERA A CONSULTA
        $appointment = PsychoAppointments::getAppointmentById($appointmentId);

        //RECUPERA A DISPONIBILIDADE DE HORÁRIO
        $availability = PsychoAvailabilities::getPsychoAvailabilitiesById($appointment->availability_id);

        $userTypeNanme = '';

        switch ($userType) {
            case UserTypes::USER:
                //VERIFICA SE A CONSULTA FOI AGENDADA POR ESTE MESMO USUÁRIO ANTERIORMENTE
                if ($userId != $appointment->user_id) {
                    return parent::getApiResponse('Error processing the request', [
                        'You can only cancel your own appointments'
                    ], 403);
                }
                break;

            case UserTypes::PSYCHOLOGIST:
                //VERIFICA SE A CONSULTA FOI AGENDADA POR ESTE MESMO PSICÓLOGO ANTERIORMENTE
                if ($userId != $availability->psychologist_id) {
                    return parent::getApiResponse('Error processing the request', [
                        'You can only cancel your own appointments'
                    ], 403);
                }
                break;

            case UserTypes::ADMIN:
                break;

            default:
                return parent::getApiResponse('Error processing the request', [
                    'Internal Server Error: Invalid user type or missing'
                ], 500);
                break;
        }

        $appointment->cancelled_by = $userId;
        $appointment->cancel_reason = $postVars["cancel_reason"] ?? null;
        $appointment->status = PsychoAppointments::STATUS_CANCELLED;
        $appointment->update();
        time() > $availability->date ? $availability->status = PsychoAvailabilities::STATUS_CANCELLED : $availability->status = PsychoAvailabilities::STATUS_AVAILABLE;
        $availability->update();

        return parent::getApiResponse('Appointment successfully cancelled', [
            'appointment' => $appointment->getPartialData()
        ]);
    }

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

<?php

namespace App\Controller\Api\Appointments;

use App\Controller\Api\Api;
use App\Model\Entity\Appointments\PsychoAvailabilities;
use App\Model\Entity\Users\User;

class Availabilites extends Api
{

    /**
     * Método responsável por retornar os itens de disponibilidade de horários do psicólogo
     *
     * @param  int $psychologistId
     * @param  string $startDate
     * @param  string $endDate
     * @return array
     */
    private static function getAvailabilitiesItems($psychologistId, $startDate, $endDate)
    {
        //HORÁRIOS
        $itens = [];

        //RESULTADOS DA PÁGINA
        $results = PsychoAvailabilities::getPsychoAvailabilitiesByPsychoIdAndDateRange($psychologistId, $startDate, $endDate);

        //RENDERIZA O ITEM
        while ($obPsychoAvailabilityItem = $results->fetchObject(PsychoAvailabilities::class)) {
            //HORÁRIOS
            $itens[] = [
                'id' => $obPsychoAvailabilityItem->id,
                'date' => $obPsychoAvailabilityItem->date,
                'status' => $obPsychoAvailabilityItem->status,
            ];
        }

        //RETORNA OS USUÁRIOS DA LISTA
        return $itens;
    }

    /**
     * Método responsável por retornar as disponibilidades de horários do psicólogo
     *
     * @param  Request $request
     * @return array
     */
    public static function getAvailabilities($request)
    {
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //OBTENDO O ID DO PSICÓLOGO ATRAVES DOS PARAMETROS PARA VALIDAÇÕES E CONSULTA
        $psychologistId = $queryParams['psychologist_id'];

        //VERIFICA SE O ID DO PSICÓLOGO FOI INFORMADO
        if (!isset($psychologistId) || !is_numeric($psychologistId)) {
            return parent::getApiResponse(
                'Error processing the request.',
                "It is necessary to provide the psychologist's ID.",
                400
            );
        }

        //VERIFICA SE O PSICÓLOGO EXISTE
        if (!User::isPsychologist($psychologistId)) {
            return parent::getApiResponse(
                'Error processing the request.',
                "The psychologist does not exist.",
                400
            );
        }

        //OBTENDO AS DATAS INICIAIS E FINAIS PELOS PARAMETROS
        $startDate = isset($queryParams['start_date']) ? $queryParams['start_date'] : date('Y-m-d H:i:s');
        $endDate = isset($queryParams['end_date']) ? $queryParams['end_date'] : date('Y-m-d H:i:s', strtotime('+1 day'));

        //DATAS EM TIMESTAMP
        $startDateTs = strtotime($startDate);
        $endDateTs = strtotime($endDate);

        //VERIFICA SE A DATA É VÁLIDA
        if (!$startDateTs || !$endDateTs) {
            return parent::getApiResponse(
                'Error processing the request.',
                "The start and end dates must be valid dates.",
                400
            );
        }

        //VERIFICA SE A DATA INICIAL É MENOR QUE A DATA FINAL
        if ($startDateTs > $endDateTs) {
            return parent::getApiResponse(
                'Error processing the request.',
                "The start date must be less than the end date.",
                400
            );
        }

        //VERIFICA SE AS DATAS INFORMADAS SÃO IGUAIS OU SUPERIORES A DATA ATUAL
        if ($startDateTs < time() || $endDateTs < time()) {
            return parent::getApiResponse(
                'Error processing the request.',
                "The start and end dates must be greater than or equal to the current date.",
                400
            );
        }



        return parent::getApiResponse(
            'Pyschologist availabilities',
            [
                'psychologist_id' => $psychologistId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'available_slots' => self::getAvailabilitiesItems($psychologistId, $startDate, $endDate)
            ],
            200
        );
    }

    /**
     * Método responspável por criar novas disponibilidades de horário para o psicólogo
     *
     * @param  Request $request
     * @return array
     */
    public static function setNewAvailability($request)
    {
        $postVars = $request->getPostVars();
        $user = $request->user;
        $userId = $user->id;

        $dates = $postVars['dates'] ?? null;

        $savedDates = [];
        $skippedDates = [];

        if (!isset($dates) || !is_array($dates)) {
            return parent::getApiResponse(
                'Error processing the dates.',
                [
                    'message' => 'It is necessary to provide the dates to create the availability.'
                ],
                400
            );
        }

        foreach ($dates as $rawDate) {
            // TRANSFORMA A DATA EM UM FORMATO ACEITO
            $date = date('Y-m-d H:i:s', strtotime($rawDate));

            // VERIFICA SE A DATA JÁ ESTÁ CADASTRADA PARA O PSICÓLOGO
            if (PsychoAvailabilities::getPsychoAvailabilitiesByPsychoIdAndDate($userId, $date) instanceof PsychoAvailabilities) {
                $skippedDates[] = $date;
                continue;
            }

            // CRIA A NOVA DISPONIBILIDADE
            $psychoAvailability = new PsychoAvailabilities();
            $psychoAvailability->psychologist_id = $userId;
            $psychoAvailability->date = $date;
            $psychoAvailability->status = 1;
            $psychoAvailability->register();
            $savedDates[] = $date;
        }

        if (!empty($skippedDates)) {
            return parent::getApiResponse(
                'Some availability dates were skipped',
                [
                    'saved_dates' => $savedDates,
                    'skipped_dates' => $skippedDates
                ],
                207
            );
        }

        return parent::getApiResponse(
            'Availability created successfully',
            [
                'saved_dates' => $savedDates
            ],
            201
        );
    }

    /**
     * Método responsável por editar o status de uma disponibilidade
     *
     * @param  Request $request
     * @return void
     */
    public static function editAvailabilityStatus($request)
    {
        $postVars = $request->getPostVars();
        $user = $request->user;
        $psychoId = $user->id;

        //VERIFICA SE O ID DA DISPONIBILIDADE FOI INFORMADO
        if (!isset($postVars['availability_id']) || !is_numeric($postVars['availability_id'])) {
            return parent::getApiResponse(
                'Error processing the request.',
                "It is necessary to provide the availability ID.",
                400
            );
        }

        //VERIFICA SE O STATUS FOI INFORMADO
        if (!isset($postVars['status']) || !is_numeric($postVars['status'])) {
            return parent::getApiResponse(
                'Error processing the request.',
                "It is necessary to provide the status.",
                400
            );
        }

        //VERIFICA SE A DISPONIBILIDADE EXISTE E SE O PSICÓLOGO É O DONO
        $psychoAvailability = !PsychoAvailabilities::getPsychoAvailabilityByPsychoIdAndId($psychoId, $postVars['availability_id']);
        if (!$psychoAvailability instanceof PsychoAvailabilities) {
            return parent::getApiResponse(
                'Error processing the request.',
                "The availability does not exist.",
                400
            );
        }

        //ATUALIZA O STATUS DA DISPONIBILIDADE
        $psychoAvailability->id = $postVars['availability_id'];
        $psychoAvailability->status = (int) $postVars['status'];
        $psychoAvailability->update();

        return parent::getApiResponse(
            'Availability status updated successfully',
            [
                'availability_id' => $psychoAvailability->id,
                'status' => $psychoAvailability->status
            ],
            200
        );
    }

    /**
     * Método responsável por permitir que um usuário agende uma disponibilidade (status = 2)
     *
     * @param  Request $request
     * @return Response
     */
    public static function scheduleAvailabilityByUser($request)
    {
        $postVars = $request->getPostVars();
        $availabilityId = (int)$postVars['availability_id'];

        // VERIFICA SE O ID DA DISPONIBILIDADE FOI INFORMADO
        if (!isset($availabilityId) || !is_numeric($availabilityId)) {
            return parent::getApiResponse(
                'Error processing the request.',
                "It is necessary to provide the availability ID.",
                400
            );
        }

        // BUSCA A DISPONIBILIDADE
        $availability = PsychoAvailabilities::getPsychoAvailabilitiesById($availabilityId);

        if (!$availability instanceof PsychoAvailabilities) {
            return parent::getApiResponse(
                'Error processing the request.',
                "The availability does not exist.",
                400
            );
        }

        // VALIDA SE O HORÁRIO É FUTURO
        if (strtotime($availability->date) <= time()) {
            return parent::getApiResponse(
                'Error processing the request.',
                "This availability is in the past and cannot be scheduled.",
                400
            );
        }

        // VERIFICA SE JÁ ESTÁ AGENDADO OU INDISPONÍVEL
        if ($availability->status != 1) {
            return parent::getApiResponse(
                'Error processing the request.',
                "This availability is no longer available.",
                409
            );
        }

        // ATUALIZA O STATUS PARA AGENDADO (2)
        $availability->status = 2;
        $availability->update();

        return parent::getApiResponse(
            'Availability scheduled successfully.',
            [
                'availability_id' => $availability->id,
                'status' => $availability->status
            ],
            200
        );
    }
}

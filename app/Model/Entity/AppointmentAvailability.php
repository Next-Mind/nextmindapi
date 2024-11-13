<?php

namespace App\Model\Entity;

use BadFunctionCallException;
use WilliamCosta\DatabaseManager\Database;

class AppointmentAvailability {    
    /**
     * ID do registro da disponibilidade de horário do psicólogo
     *
     * @var integer
     */
    public $id;
    
    /**
     * ID do psicólogo que detém o horário disponível
     *
     * @var integer
     */
    public $psicologo_id;
    
    /**
     * Data do consulta que está disponível para agendamento
     *
     * @var string
     */
    public $data_disponivel;
    
    /**
     * Hora início da consulta disponível
     *
     * @var string
     */
    public $hora_inicio;
    
    /**
     * Hora final da consulta disponível
     *
     * @var string
     */
    public $hora_fim;
    
    /**
     * Situação em que se encontra o horário (Disponivel, agendado, cancelado)
     *
     * @var integer
     */
    public $situacao;
    
    /**
     * ID do aluno que está planejando agendar a consulta, para evitar que dois alunos agendem a mesma consulta ao mesmo tempo
     *
     * @var integer
     */
    public $reservada_por;
    
    /**
     * Data e hora que foi realizado a reserva
     *
     * @var string
     */
    public $data_reserva;
    
    /**
     * Método responsável por cadastrar a instância atual no banco de dados
     *
     * @return boolean
     */
    public function cadastrar() {
        //INSERE A INSTANCIA ATUAL NO BANCO DE DADOS
        $this->id = (new Database('disponibilidade_psicologos'))->insert([
            'psicologo_id' => $this->psicologo_id,
            'data_disponivel' => $this->data_disponivel,
            'hora_inicio' => $this->hora_inicio,
            'hora_fim' => $this->hora_fim,
            'situacao' => $this->situacao,
            'reservada_por' => $this->reservada_por,
            'data_reserva' => $this->data_reserva,
        ]);
        //SUCESSO
        return true;
    }
    
    /**
     * Método responsável por atualizar os dados no banco de dados
     *
     * @return boolean
     */
    public function atualizar(){
        //ATUALIZA O REGISTRO COM A INSTANCIA ATUAL
        return (new Database('disponibilidade_psicologos'))->update('id = '.$this->id,[
            'psicologo_id' => $this->psicologo_id,
            'data_disponivel' => $this->data_disponivel,
            'hora_inicio' => $this->hora_inicio,
            'hora_fim' => $this->hora_fim,
            'situacao' => $this->situacao,
            'reservada_por' => $this->reservada_por,
            'data_reserva' => $this->data_reserva,
        ]);
    }
    
    /**
     * Método responsável por retornar os horários disponíveis de um psicólogo
     *
     * @param  integer $id
     * @return PDOStatement
     */
    public static function getAppointmentsAvailabilityByPsychologist($id) {
        return self::getAppointmentsAvailability('psicologo_id = '.$id);
    }
    
    /**
     * Método responsável por retornar um horário disponível baseado no seu ID
     *
     * @param  integer $id
     * @return AppointmentAvailability
     */
    public static function getAppointmentAvailabilityById($id){
        return self::getAppointmentsAvailability('id = '.$id)->fetchObject(self::class);
    }
    
    /**
     * Método responsável por retornar horários disponíveis
     *
     * @param  string $where
     * @param  string $order
     * @param  string $limit
     * @param  string $fields
     * @return PDOStatement
     */
    public static function getAppointmentsAvailability($where = null, $order = null, $limit = null, $fields = '*'){
        return (new Database('disponibilidade_psicologos'))->select($where,$order,$limit,$fields);
    }

    public static function isAppointmentReserved($id){
        $obAppointment = self::getAppointmentAvailabilityById($id);

        if(!$obAppointment instanceof (self::class)) {
            throw new \Exception("Horário não econtrado",400);
        }

        if(!empty($obAppointment->reservada_por) && strtotime($obAppointment->data_reserva) > time()) {
            return true;
        }
        return false;
    }
}

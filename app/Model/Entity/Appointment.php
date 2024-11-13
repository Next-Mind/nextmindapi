<?php

namespace App\Model\Entity;

class Appointment {
    
    /**
     * ID do registro da consulta agendada
     *
     * @var integer
     */
    public $id;
    
    /**
     * ID do aluno que agendou a consulta
     *
     * @var integer
     */
    public $aluno_id;
    
    /**
     * ID do psicólogo responsável pela consulta
     *
     * @var integer
     */
    public $psicologo_id;
    
    /**
     * Data e hora da consulta
     *
     * @var string
     */
    public $data_hora;
    
    /**
     * Status da consulta ('Agendada', 'Cancelada', 'Reagendada' ,'Concluida')
     *
     * @var string
     */
    public $status;
    
    /**
     * Situação em que se encontra a consulta (0 - cancelada, 1 - agendada, 2 - realizada)
     *
     * @var int
     */
    public $situacao;
    
    /**
     * Data e hora em que foi realizada o agendamento da consulta
     *
     * @var string
     */
    public $data_criacao;
    
    /**
     * Data e hora que foi realizado o reagendamento, cancelamento ou conclusão da consulta
     *
     * @var string
     */
    public $data_atualizacao;
}
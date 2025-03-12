<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class UserAnswer
{
    /**
     * ID da Resposta
     *
     * @var int
     */
    public $id;

    /**
     * ID do usuário que respondeu
     *
     * @var int
     */
    public $usuario_id;

    /**
     * ID da questão
     *
     * @var string
     */
    public $questao_id;

    /**
     * Resposta do usuário
     *
     * @var string
     */
    public $resposta;

    /**
     * Data de cadastro da resposta no banco
     *
     * @var string
     */
    public $data_cadastro;

    /**
     * Data de atualização da resposta no banco
     *
     * @var string
     */
    public $data_atualizacao;

    /**
     * Método responsável por cadastrar a instãncia atual de resposta no banco de dados
     *
     * @return boolean
     */
    public function cadastrar()
    {
        $this->id = (new Database('usuarios_resposta_questionario'))->insert([
            'usuario_id' => $this->usuario_id,
            'questao_id' => $this->questao_id,
            'resposta' => $this->resposta,
            'data_cadastro' => (new \Datetime())->format('Y-m-d H:i:s'),
            'data_atualizacao' => (new \Datetime())->format('Y-m-d H:i:s')
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar a instãncia atual no banco de dados
     *
     * @return boolean
     */
    public function atualizar()
    {
        return (new Database('usuarios_resposta_questionario'))->update('id= ' . $this->id, [
            'usuario_id' => $this->usuario_id,
            'questao_id' => $this->questao_id,
            'resposta' => $this->resposta,
            'data_cadastro' => $this->data_cadastro,
            'data_atualizacao' => (new \Datetime())->format('Y-m-d H:i:s')
        ]);
    }
}

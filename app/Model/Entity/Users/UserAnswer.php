<?php

namespace App\Model\Entity\Users;

use \WilliamCosta\DatabaseManager\Database;

class UserAnswer
{
    /**
     * ID da Resposta
     *
     * @var int
     */
    public int $id;

    /**
     * ID do usuário que respondeu
     *
     * @var int
     */
    public int $user_id;

    /**
     * ID da questão
     *
     * @var string
     */
    public string $question_id;

    /**
     * Resposta do usuário
     *
     * @var string
     */
    public string $answer;

    /**
     * Data de cadastro da resposta no banco
     *
     * @var string
     */
    public string $created_at;

    /**
     * Data de atualização da resposta no banco
     *
     * @var string
     */
    public string $updated_at;

    /**
     * Método responsável por cadastrar a instãncia atual de resposta no banco de dados
     *
     * @return boolean
     */
    public function register()
    {
        $this->id = (new Database('users_questionnaire_answers'))->insert([
            'user_id' => $this->user_id,
            'question_id' => $this->question_id,
            'answer' => $this->answer,
            'created_at' => (new \Datetime())->format('Y-m-d H:i:s'),
            'updated_at' => (new \Datetime())->format('Y-m-d H:i:s')
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar a instãncia atual no banco de dados
     *
     * @return boolean
     */
    public function update()
    {
        return (new Database('users_questionnaire_answers'))->update('id= ' . $this->id, [
            'user_id' => $this->user_id,
            'question_id' => $this->question_id,
            'answer' => $this->answer,
            'created_at' => $this->created_at,
            'updated_at' => (new \Datetime())->format('Y-m-d H:i:s')
        ]);
    }
}

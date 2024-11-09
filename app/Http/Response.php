<?php

namespace App\Http;

class Response{
    
    /**
     * Código do Status HTTP da Response
     *
     * @var int
     */
    private $httpCode = 200;
    
    /**
     * Cabeçalho do Response
     *
     * @var array
     */
    private $headers = [];
    
    /**
     * Tipo de conteúdo que está sendo retornado
     *
     * @var string
     */
    private $contentType = 'text/html';
    
    /**
     * Conteúdo do response
     *
     * @var mixed
     */
    private $content;
    
    /**
     * Método responsável por iniciar a classe e definir os valores
     *
     * @param  int $httpCode
     * @param  mixed $content
     * @param  string $contentType
     */
    public function __construct($httpCode,$content,$contentType = 'text/html') {
        $this->httpCode = $httpCode;
        $this->content  = $content;
        $this->setContentType($contentType);
    }
    
    /**
     * Método responsável o Content Type do Response
     *
     * @param  string $contentType
     * @return void
     */
    public function setContentType($contentType){  
        $this->contentType = $contentType;
        $this->addHeader('Content-Type',$contentType);
    }
    
    /**
     * Método responsável por adicionar um registro no cabeçalho do Response
     *
     * @param  string $key
     * @param  string $value
     * @return void
     */
    public function addHeader($key,$value){
        $this->headers[$key] = $value;
    }
    
    /**
     * Método responsável por enviar os headers para o navegador
     *
     */
    private function sendHeaders(){
        //STATUS
        http_response_code($this->httpCode);

        //ENVIAR HEADERS
        foreach($this->headers as $key=>$value){
            header($key.':'.$value);
        }
    }
    
    /**
     * Método responsável por enviar a resposta para o usuário
     *
     * @return void
     */
    public function sendResponse(){
        //ENVIA OS HEADERS
        $this->sendHeaders();

        //IMPRIME O CONTEÚDO
        switch($this->contentType) {
            case 'text/html':
                $this->convertToUtf8();
                echo $this->content;
                exit;
            case 'application/json':
                $this->convertToUtf8();
                echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
        }
    }

    public function convertToUtf8(){
        array_walk_recursive($this->content,function(&$item){
            $item = mb_convert_encoding($item,'UTF-8','ISO-8859-1');
        });
    }
}
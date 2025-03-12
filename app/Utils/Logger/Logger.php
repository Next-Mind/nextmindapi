<?php

namespace App\Utils\Logger;

class Logger
{
    private string $runtimeType;

    public function __construct(string $runtimeType)
    {
        $this->runtimeType = $runtimeType;
    }

    // Retorna o caminho completo do arquivo de log
    private function getLogFilePath(): string
    {
        // Obtém o diretório dos logs a partir da variável de ambiente, ou usa um diretório padrão
        $dir = __DIR__ . '/../../../' . '/.logs/' . date('Y-m-d');

        // Verifica a existência do diretório e cria se necessário
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // Define o nome do arquivo de log em letras minúsculas (ex: router.log)
        $filename = strtolower($this->runtimeType) . '.log';

        // Retorna o caminho completo, garantindo que a separação de diretórios seja correta
        return rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
    }

    // Método privado para registrar mensagens com um determinado nível
    private function log(string $level, string $message): void
    {
        // Obtém a data e hora atual no formato desejado
        $date = date('Y-m-d H:i:s');
        // Formata a mensagem de log
        $logLine = sprintf("[%s] [%s] %s%s", $date, strtoupper($level), $message, PHP_EOL);
        // Escreve no arquivo, adicionando a nova mensagem (FILE_APPEND)
        file_put_contents($this->getLogFilePath(), $logLine, FILE_APPEND);
    }

    // Método para log de informações
    public function info(string $message): void
    {
        $this->log('info', $message);
    }

    // Método para log de avisos
    public function warning(string $message): void
    {
        $this->log('warning', $message);
    }

    // Método para log de erros
    public function error(string $message): void
    {
        $this->log('error', $message);
    }

    public function debug(string $message): void
    {
        if (filter_var(getenv('DEBUG_MODE'), FILTER_VALIDATE_BOOLEAN)) {
            $this->log('debug', $message);
        }
    }
}

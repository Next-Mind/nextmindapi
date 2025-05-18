<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');

require __DIR__ . '/../../../../vendor/autoload.php';

use \WilliamCosta\DotEnv\Environment;
use \WilliamCosta\DatabaseManager\Database;
use App\Utils\Logger\Logger;

Environment::load(__DIR__ . '/../../../../');

// Configuração do banco
Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_PORT')
);

// Logger da cron
$logger = new Logger('cron_availabilities');

try {
    $logger->info("Iniciando verificação de horários com status 2...");

    $db = new Database('psycho_availabilities');

    $limite = date("Y-m-d H:i:s", strtotime("-5 minutes"));

    $stmt = $db->select("status = '2' AND updated_at <= '$limite'");

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        $logger->info("Nenhum registro encontrado para liberar.");
        exit;
    }

    $total = 0;
    foreach ($results as $row) {
        $updated = $db->update(
            'id = ' . (int)$row['id'], // segurança no cast do ID
            [
                'psychologist_id' => $row['psychologist_id'],
                'date' => $row['date'],
                'status' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );

        if ($updated) {
            $total++;
        }
    }

    $logger->info("Liberação concluída com sucesso. Linhas afetadas: {$total}");
} catch (Exception $e) {
    $logger->error("Erro ao executar cron: " . $e->getMessage());
}

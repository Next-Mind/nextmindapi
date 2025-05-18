<?php

require __DIR__ . '/vendor/autoload.php';

use \WilliamCosta\DatabaseManager\Database;
use App\Utils\Logger\Logger;

// Instancia o logger para esse cron
$logger = new Logger('cron_availabilities');

try {
    $logger->info("Iniciando verificação de horários ocupados...");

    $db = new Database('psycho_availabilities');

    $affected = $db->execute("
        UPDATE psycho_availabilities
        SET status = 1, updated_at = NOW()
        WHERE status = 2
          AND updated_at <= (NOW() - INTERVAL 15 MINUTE)
    ");

    if ($affected !== false) {
        $logger->info("Liberações realizadas com sucesso. Linhas afetadas: {$affected}");
    } else {
        $logger->warning("Nenhuma liberação realizada ou erro silencioso na execução.");
    }
} catch (Exception $e) {
    $logger->error("Erro ao executar cron: " . $e->getMessage());
}

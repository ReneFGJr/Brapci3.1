<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class Z3950Work extends BaseCommand
{
    protected $group = 'FIND';
    protected $name = 'z3950:work';
    protected $description = 'Processa até 20 pedidos pendentes de coleta Z39.50.';

    public function run(array $params)
    {
        if (!function_exists('yaz_connect')) {
            CLI::error('Extensão PHP-YAZ necessária para processar a fila.');
            return EXIT_ERROR;
        }
        $queue = new \App\Models\Z3950\Queue();
        $collector = new \App\Models\Z3950\Index();
        $count = 0;
        while ($count < 20 && $queue->processOne([$collector, 'searchByISBN'])) $count++;
        CLI::write("Pedidos processados: {$count}");
    }
}

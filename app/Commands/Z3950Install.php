<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class Z3950Install extends BaseCommand
{
    protected $group = 'FIND';
    protected $name = 'z3950:install';
    protected $description = 'Aplica somente a migration da fila Z39.50.';

    public function run(array $params)
    {
        $runner = service('migrations');
        foreach ($runner->getHistory('default') as $migration) {
            if ($migration->class === 'App\\Database\\Migrations\\CreateZ3950Queue') {
                CLI::write('Fila Z39.50 já instalada.');
                return;
            }
        }
        $runner->force(APPPATH . 'Database/Migrations/2026-10-23-120000_CreateZ3950Queue.php', 'App', 'default');
        CLI::write('Fila Z39.50 instalada.');
    }
}

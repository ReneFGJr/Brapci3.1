<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class Z3950Install extends BaseCommand
{
    protected $group = 'FIND';
    protected $name = 'z3950:install';
    protected $description = 'Instala e migra o banco, a fila e os servidores Z39.50.';

    public function run(array $params)
    {
        $runner = service('migrations');
        $history = array_column($runner->getHistory('default'), 'class');
        $migrations = [
            'CreateZ3950Queue' => '2026-10-23-120000_CreateZ3950Queue.php',
            'MoveZ3950Database' => '2026-10-23-130000_MoveZ3950Database.php',
        ];
        foreach ($migrations as $class => $file) {
            if (!in_array('App\\Database\\Migrations\\' . $class, $history, true)) {
                $runner->force(APPPATH . 'Database/Migrations/' . $file, 'App', 'default');
            }
        }
        CLI::write('Banco, fila e servidores Z39.50 instalados.');
    }
}

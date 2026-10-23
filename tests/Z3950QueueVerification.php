<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class VerifyQueue extends BaseCommand
{
    protected $group = 'FIND';
    protected $name = 'z3950:verify-local';
    protected $description = 'Validação transacional local da fila.';
    public function run(array $params)
    {
        $db = db_connect();
        $db->transBegin();
        try {
            $check = static function ($condition, $message) { if (!$condition) throw new \RuntimeException($message); };
            $db->table('z3950_queue')->delete(['isbn' => '9780306406157']);
            $queue = new \App\Models\Z3950\Queue();
            $a = $queue->enqueue('0-306-40615-2');
            $b = $queue->enqueue('9780306406157');
            $check($a['id'] === $b['id'], 'Pedido duplicado');
            $check($a['isbn'] === '9780306406157', 'Conversão ISBN-10');
            // Isolate test rows inside the transaction, rolled back below.
            $db->table('z3950_queue')->where('id !=', $a['id'])->update(['status' => 'completed']);
            $queue->processOne(static function () { throw new \RuntimeException('Falha simulada'); });
            $job = $queue->find($a['id']);
            $check($job['status'] === 'pending' && (int) $job['attempts'] === 1 && $job['available_at'] > date('Y-m-d H:i:s'), 'Retry');
            $queue->update($a['id'], ['available_at' => '2000-01-01 00:00:00']);
            $queue->processOne(static function () { return '<record>teste</record>'; });
            $job = $queue->find($a['id']);
            $check($job['status'] === 'completed' && $job['result'] === '<record>teste</record>', 'Resultado');
            $queue->update($a['id'], ['status' => 'processing', 'locked_at' => '2000-01-01 00:00:00', 'lock_token' => 'abandoned']);
            $queue->processOne(static function () { return null; });
            $check($queue->find($a['id'])['status'] === 'not_found', 'Recuperação de trava');
            $queue->update($a['id'], ['status' => 'pending', 'attempts' => 4]);
            $queue->processOne(static function () { throw new \RuntimeException('Falha final'); });
            $check($queue->find($a['id'])['status'] === 'failed', 'Limite de tentativas');
            try { $queue->enqueue('9780306406158'); throw new \RuntimeException('ISBN incorreto aceito'); }
            catch (\InvalidArgumentException $e) {}
            CLI::write('OK: deduplicação, ISBN, retry, resultado, recuperação de trava e limite de tentativas.');
        } finally {
            $db->transRollback();
        }
    }
}

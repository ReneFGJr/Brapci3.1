<?php
namespace App\Models\Z3950;

use App\Libraries\Z3950Isbn;
use CodeIgniter\Model;

class Queue extends Model
{
    protected $table = 'z3950_queue';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['isbn', 'status', 'attempts', 'available_at', 'locked_at', 'lock_token', 'result', 'last_error', 'created_at', 'updated_at'];

    public function enqueue(string $value): array
    {
        $isbn = Z3950Isbn::normalize($value);
        if ($isbn === '') throw new \InvalidArgumentException('ISBN inválido.');
        $now = date('Y-m-d H:i:s');
        // Unique ISBN makes simultaneous requests and retries idempotent.
        $table = $this->db->protectIdentifiers($this->db->prefixTable($this->table));
        $this->db->query("INSERT INTO {$table} (isbn, status, available_at, created_at, updated_at) VALUES (?, 'pending', ?, ?, ?) ON DUPLICATE KEY UPDATE isbn = VALUES(isbn)", [$isbn, $now, $now, $now]);
        return $this->where('isbn', $isbn)->first();
    }

    public function processOne(callable $collect): bool
    {
        $now = date('Y-m-d H:i:s');
        // Recover workers that died while holding a job.
        $this->builder()->where('status', 'processing')->where('locked_at <', date('Y-m-d H:i:s', time() - 300))
            ->update(['status' => 'pending', 'lock_token' => null, 'locked_at' => null]);
        $job = $this->where('status', 'pending')->where('available_at <=', $now)->orderBy('id')->first();
        if (!$job) return false;
        $token = bin2hex(random_bytes(24));
        $attempt = (int) $job['attempts'] + 1;
        $this->builder()->where('id', $job['id'])->where('status', 'pending')->update([
            'status' => 'processing', 'attempts' => $attempt, 'locked_at' => $now, 'lock_token' => $token, 'updated_at' => $now,
        ]);
        if ($this->db->affectedRows() !== 1) return true;
        try {
            $record = $collect($job['isbn']);
            $data = ['status' => $record ? 'completed' : 'not_found', 'result' => $record ?: null, 'last_error' => null];
        } catch (\Throwable $e) {
            $data = ['status' => $attempt >= 5 ? 'failed' : 'pending', 'last_error' => $e->getMessage(),
                'available_at' => date('Y-m-d H:i:s', time() + min(3600, 60 * (2 ** min($attempt, 6))))];
        }
        $data += ['locked_at' => null, 'lock_token' => null, 'updated_at' => date('Y-m-d H:i:s')];
        $this->builder()->where('id', $job['id'])->where('lock_token', $token)->update($data);
        return true;
    }
}

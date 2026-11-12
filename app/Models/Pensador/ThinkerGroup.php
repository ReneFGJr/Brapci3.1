<?php
namespace App\Models\Pensador;
use CodeIgniter\Model;
class ThinkerGroup extends Model
{
    protected $table = 'pensador_grupos';
    protected $allowedFields = ['nome'];
    protected $returnType = 'array';

    public function members(int $id): array
    {
        return $this->db->table('pensador_grupo_membros m')->select('p.*')
            ->join('pensadores p', 'p.id = m.pensador_id')->where('m.grupo_id', $id)
            ->orderBy('p.nome', 'ASC')->get()->getResultArray();
    }

    public function saveMembers(string $name, array $ids, int $groupId = 0): int
    {
        $name = trim($name);
        if ($groupId === 0 && ($name === '' || mb_strlen($name) > 255)) {
            throw new \InvalidArgumentException('Informe um nome de grupo com até 255 caracteres.');
        }
        foreach ($ids as $id) {
            if (!is_string($id) || !preg_match('/^[1-9][0-9]*$/D', $id)) {
                throw new \InvalidArgumentException('Seleção de pensadores inválida.');
            }
        }
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (!$ids) {
            throw new \InvalidArgumentException('Selecione ao menos um pensador.');
        }
        $this->db->transBegin();
        try {
            if ($this->db->table('pensadores')->whereIn('id', $ids)->countAllResults() !== count($ids)) {
                throw new \InvalidArgumentException('Um dos pensadores selecionados não existe mais.');
            }
            if ($groupId > 0) {
                if (!$this->find($groupId)) {
                    throw new \InvalidArgumentException('Grupo não encontrado.');
                }
            } else {
                $groupId = (int) $this->insert(['nome' => $name]);
            }
            $existing = array_map('intval', array_column($this->members($groupId), 'id'));
            foreach (array_diff($ids, $existing) as $id) {
                $this->db->table('pensador_grupo_membros')->insert(['grupo_id' => $groupId, 'pensador_id' => $id]);
            }
            if (!$groupId || $this->db->transStatus() === false) {
                throw new \RuntimeException('Não foi possível salvar o grupo.');
            }
            $this->db->transCommit();
            return $groupId;
        } catch (\Throwable $error) {
            $this->db->transRollback();
            throw $error;
        }
    }

    public function removeMember(int $groupId, int $thinkerId): void
    {
        $this->db->table('pensador_grupo_membros')->where('grupo_id', $groupId)
            ->where('pensador_id', $thinkerId)->delete();
    }
}

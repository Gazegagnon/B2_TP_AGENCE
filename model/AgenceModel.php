<?php
class AgenceModel extends AbstractModel
{
    protected string $table = "agence";
    protected string $primaryKey = "id";

    public function getAgenceById(int $id): ?Agence
    {
        $res = $this->getById($id);
        if (!$res) return null;

        $agence = new Agence($res);
        return $agence;
    }

    public function getAllAgences(): array
    {
        $tab = $this->getAll();
        return array_map(fn($row) => new Agence($row), $tab);
    }
}

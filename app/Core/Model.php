<?php

namespace Ecommerce\Shop\Core;

use PDO;

class Model
{
    protected PDO $db;

    protected string $table = '';

    protected string $primaryKey = 'id';


    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }


    public function findAll(string $orderBy = '', string $direction = 'ASC'): array
    {
        $sql = "SELECT * FROM {$this->table}";

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$direction}";
        }

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1"
        );

        $stmt->execute([
            ':id' => $id
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }


    public function findBy(array $conditions): array
    {
        $where = implode(
            ' AND ',
            array_map(
                fn($key) => "{$key} = :{$key}",
                array_keys($conditions)
            )
        );

        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$where}"
        );

        $stmt->execute($conditions);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function findOneBy(array $conditions): ?array
    {
        $where = implode(
            ' AND ',
            array_map(
                fn($key) => "{$key} = :{$key}",
                array_keys($conditions)
            )
        );

        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$where} LIMIT 1"
        );

        $stmt->execute($conditions);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }


    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));

        $placeholders = implode(
            ', ',
            array_map(
                fn($key) => ":{$key}",
                array_keys($data)
            )
        );


        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})"
        );


        $stmt->execute($data);

        return (int)$this->db->lastInsertId();
    }


    public function update(int $id, array $data): bool
    {
        $set = implode(
            ', ',
            array_map(
                fn($key) => "{$key} = :{$key}",
                array_keys($data)
            )
        );


        $data[':id'] = $id;


        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = :id"
        );


        return $stmt->execute($data);
    }


    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id"
        );


        return $stmt->execute([
            ':id' => $id
        ]);
    }


    public function count(): int
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) FROM {$this->table}"
        );

        return (int)$stmt->fetchColumn();
    }


    public function paginate(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;


        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset"
        );


        $stmt->bindValue(
            ':limit',
            $perPage,
            PDO::PARAM_INT
        );


        $stmt->bindValue(
            ':offset',
            $offset,
            PDO::PARAM_INT
        );


        $stmt->execute();


        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $this->count(),
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($this->count() / $perPage)
        ];
    }
}
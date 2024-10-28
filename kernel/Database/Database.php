<?php

namespace App\Kernel\Database;

use App\Kernel\Config\ConfigInterface;

class Database implements DatabaseInterface
{
    private \PDO $pdo;

    public function __construct(
        private ConfigInterface $config,
    ) {
        $this->connect();
    }

    public function insert(string $table, array $data): int|false
    {
        $fields = array_keys($data);

        $columns = implode(', ', $fields);
        $binds = implode(', ', array_map(fn ($field) => ":$field", $fields));

        $sql = "INSERT INTO $table ($columns) VALUES ($binds)";

        $stmt = $this->pdo->prepare($sql);

        try {
            $stmt->execute($data);
        } catch (\PDOException $exception) {
            dd('xaxa');
            return false;
        }

        return (int) $this->pdo->lastInsertId();
    }

    public function first(string $table, array $conditions = [], string $orderBy = ''): ?array
    {
        $where = '';
        $params = [];

        if (count($conditions) > 0) {
            $clauses = [];
            foreach ($conditions as $field => $value) {
                if (is_array($value)) {
                    $clauses[] = "$field";
                    $params = array_merge($params, $value);
                } else {
                    $clauses[] = "$field = ?";
                    $params[] = $value;
                }
            }
            $where = ' WHERE '.implode(' AND ', $clauses);
        }

        $order = $orderBy ? " ORDER BY $orderBy" : '';
        $sql = "SELECT * FROM $table $where $order LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function connect()
    {

        $driver = $this->config->get('database.driver');
        $host = $this->config->get('database.host');
        $port = $this->config->get('database.port');
        $database = $this->config->get('database.database');
        $username = $this->config->get('database.username');
        $password = $this->config->get('database.password');
        $charset = $this->config->get('database.charset');

        try {
            $this->pdo = new \PDO(
                "$driver:host=$host;port=$port;dbname=$database;charset=$charset",
                $username,
                $password
            );
        } catch (\PDOException $exception) {
            exit("Database connection failed: {$exception->getMessage()}");
        }

    }

    public function get(string $table, array $conditions = []): array
    {
        $where = '';
        $params = [];

        if (count($conditions) > 0) {
            $clauses = [];
            foreach ($conditions as $field => $value) {
                if (strpos($field, '?') !== false) {
                    $clauses[] = $field;
                    $params = array_merge($params, $value);
                } else {
                    $clauses[] = "$field = :$field";
                    $params[$field] = $value;
                }
            }
            $where = ' WHERE '.implode(' AND ', $clauses);
        }

        $sql = "SELECT * FROM $table $where";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

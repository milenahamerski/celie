<?php

namespace Core\Database\ActiveRecord;

use Core\Database\Database;
use PDO;

class BelongsToMany
{
    /** @var array<string, mixed> */
    protected array $pivotColumns = [];

    public function __construct(
        private Model  $model,
        private string $related,
        private string $pivot_table,
        private string $from_foreign_key,
        private string $to_foreign_key,
    ) {}

    public function withPivot(string ...$columns): self
    {
        $this->pivotColumns = $columns;
        return $this;
    }

    /**
     * @return array<Model>
     */
    public function get()
    {
        $fromTable = $this->model::table();
        $toTable = $this->related::table();

        $attributes = $toTable . '.id, ';
        foreach ($this->related::columns() as $column) {
            $attributes .= $toTable . '.' . $column . ', ';
        }

        foreach ($this->pivotColumns as $column) {
            $attributes .= $this->pivot_table . '.' . $column . ' AS pivot_' . $column . ', ';
        }
        $attributes = rtrim($attributes, ', ');

        $sql = <<<SQL
            SELECT 
                {$attributes}
            FROM 
                {$fromTable}
            JOIN {$this->pivot_table} ON {$fromTable}.id = {$this->pivot_table}.{$this->from_foreign_key}
            JOIN {$toTable} ON {$toTable}.id = {$this->pivot_table}.{$this->to_foreign_key}
            WHERE 
                {$fromTable}.id = :id
        SQL;

        $pdo = Database::getDatabaseConn();
        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $this->model->id);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $models = [];
        foreach ($rows as $row) {
            $modelData = [];
            $pivotData = new \stdClass();

            foreach ($row as $key => $value) {
                if (str_starts_with($key, 'pivot_')) {
                    $pivotKey = substr($key, 6);
                    $pivotData->$pivotKey = $value;
                } else {
                    $modelData[$key] = $value;
                }
            }

            $model = new $this->related($modelData);
            $model->pivot = $pivotData;
            $models[] = $model;
        }

        return $models;
    }

    public function count(): int
    {
        $fromTable = $this->model::table();
        $toTable = $this->related::table();

        $sql = <<<SQL
        SELECT 
            count({$toTable}.id) as total
        FROM 
            {$fromTable}
        JOIN {$this->pivot_table} ON {$fromTable}.id = {$this->pivot_table}.{$this->from_foreign_key}
        JOIN {$toTable} ON {$toTable}.id = {$this->pivot_table}.{$this->to_foreign_key}
        WHERE 
            {$fromTable}.id = :id
        SQL;

        $pdo = Database::getDatabaseConn();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $this->model->id);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return (int)$rows[0]['total'];
    }

    /**
     * @param int $related_id
     * @param array<string, mixed> $pivotData
     * @return bool
     */
    public function attach(int $related_id, array $pivotData = []): bool
    {
        $pdo = Database::getDatabaseConn();

        $sqlCheck = "SELECT COUNT(*) FROM {$this->pivot_table} WHERE {$this->from_foreign_key} = :from_id AND {$this->to_foreign_key} = :to_id";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([
            ':from_id' => $this->model->id,
            ':to_id' => $related_id,
        ]);
        if ($stmtCheck->fetchColumn() > 0) {
            return true;
        }

        $columns = [$this->from_foreign_key, $this->to_foreign_key];
        $placeholders = [':from_id', ':to_id'];
        $params = [
            ':from_id' => $this->model->id,
            ':to_id' => $related_id,
        ];

        foreach ($pivotData as $column => $value) {
            $columns[] = $column;
            $placeholder = ':' . $column;
            $placeholders[] = $placeholder;
            $params[$placeholder] = $value;
        }

        $columnsSql = implode(', ', $columns);
        $placeholdersSql = implode(', ', $placeholders);

        $sql = "INSERT INTO {$this->pivot_table} ({$columnsSql}) VALUES ({$placeholdersSql})";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute($params);
    }

    /**
     * @param int $relatedId
     * @return void
     */
    public function detach(int $relatedId): void
    {
        $pdo = Database::getDatabaseConn();

        $sql = "DELETE FROM {$this->pivot_table} WHERE {$this->from_foreign_key} = :from_id AND {$this->to_foreign_key} = :to_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':from_id' => $this->model->id,
            ':to_id' => $relatedId,
        ]);
    }
}

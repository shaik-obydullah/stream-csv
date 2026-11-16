<?php

declare(strict_types=1);

namespace App\Export;

use PDO;

final class OrdersExporter
{
    /** @var list<string> */
    private array $columns = [
        'order_id',
        'customer_name',
        'customer_address',
        'order_amount',
        'created_time',
        'created_date',
    ];

    /** @var list<string> */
    private array $headers = [
        'ID',
        'Customer Name',
        'Customer Address',
        'Amount',
        'Time',
        'Date',
    ];

    public function __construct(
        private readonly PDO $pdo,
        private readonly string $table = 'orders',
        private readonly string $primaryKey = 'order_id',
        private readonly int $batchSize = 1000,
    ) {
    }

    public function totalCount(): int
    {
        $sql = sprintf('SELECT COUNT(*) AS total FROM `%s`', $this->table);
        $stmt = $this->pdo->query($sql);
        $row = $stmt ? $stmt->fetch() : false;
        return (int) ($row['total'] ?? 0);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function preview(int $limit = 10): array
    {
        $limit = max(1, $limit);
        $cols = $this->columnList();
        $sql = sprintf(
            'SELECT %s FROM `%s` ORDER BY `%s` ASC LIMIT %d',
            $cols,
            $this->table,
            $this->primaryKey,
            $limit
        );
        $stmt = $this->pdo->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    /**
     * Stream CSV to the given writable stream/resource.
     *
     * @param resource $output
     */
    public function stream($output): int
    {
        if (!is_resource($output)) {
            throw new \InvalidArgumentException('Output must be a stream resource.');
        }

        fputcsv($output, $this->headers);

        $cols = $this->columnList();
        $sql = sprintf(
            'SELECT %s FROM `%s` WHERE `%s` > :lastId ORDER BY `%s` ASC LIMIT :limit',
            $cols,
            $this->table,
            $this->primaryKey,
            $this->primaryKey
        );
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $this->batchSize, PDO::PARAM_INT);

        $lastId = 0;
        $written = 0;

        while (true) {
            $stmt->bindValue(':lastId', $lastId, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            if ($rows === []) {
                break;
            }

            foreach ($rows as $row) {
                $line = [];
                foreach ($this->columns as $column) {
                    $line[] = $row[$column] ?? '';
                }
                fputcsv($output, $line);
                $written++;
                $lastId = (int) $row[$this->primaryKey];
            }

            if (count($rows) < $this->batchSize) {
                break;
            }
        }

        return $written;
    }

    /**
     * @return list<string>
     */
    public function previewHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return list<string>
     */
    public function previewColumns(): array
    {
        return $this->columns;
    }

    private function columnList(): string
    {
        $quoted = array_map(fn (string $c): string => sprintf('`%s`', $c), $this->columns);
        return implode(', ', $quoted);
    }
}

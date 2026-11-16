<?php
/** @var list<array<string, mixed>> $rows */
/** @var int $total */
/** @var list<string> $headers */
/** @var list<string> $columns */

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StreamCSV</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">StreamCSV</h1>
            <p class="text-muted mb-0">Previewing <?= e((string) count($rows)) ?> of <?= e(number_format($total)) ?> rows from <code>orders</code>.</p>
        </div>
        <a href="/export" class="btn btn-primary">Download CSV</a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-dark">
                <tr>
                    <?php foreach ($headers as $header): ?>
                        <th scope="col"><?= e($header) ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php if ($rows === []): ?>
                    <tr>
                        <td colspan="<?= e((string) count($headers)) ?>" class="text-center text-muted py-4">No rows found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <td><?= e((string) ($row[$column] ?? '')) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <p class="text-muted small mt-3 mb-0">Export streams the full table as CSV using keyset pagination.</p>
</div>
</body>
</html>

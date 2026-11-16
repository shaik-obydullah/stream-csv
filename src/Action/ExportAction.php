<?php

declare(strict_types=1);

namespace App\Action;

use App\Export\OrdersExporter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Stream;

final class ExportAction
{
    public function __construct(private readonly OrdersExporter $exporter)
    {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $filename = 'orders_export_' . date('Y-m-d_His') . '.csv';

        $stream = fopen('php://temp', 'w+b');
        if ($stream === false) {
            throw new \RuntimeException('Unable to open temporary stream.');
        }

        $this->exporter->stream($stream);
        rewind($stream);

        $body = new Stream($stream);

        return $response
            ->withHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->withHeader('Pragma', 'no-cache')
            ->withBody($body);
    }
}

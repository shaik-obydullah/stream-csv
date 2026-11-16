<?php

declare(strict_types=1);

namespace App\Action;

use App\Export\OrdersExporter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HomeAction
{
    public function __construct(
        private readonly OrdersExporter $exporter,
        private readonly string $templatesPath,
        private readonly int $previewLimit = 10,
    ) {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $rows = $this->exporter->preview($this->previewLimit);
        $total = $this->exporter->totalCount();
        $headers = $this->exporter->previewHeaders();
        $columns = $this->exporter->previewColumns();

        ob_start();
        $template = $this->templatesPath . '/home.php';
        require $template;
        $html = (string) ob_get_clean();

        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
    }
}

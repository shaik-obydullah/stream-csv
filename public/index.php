<?php

declare(strict_types=1);

use App\Action\ExportAction;
use App\Action\HomeAction;
use App\Database\ConnectionFactory;
use App\Export\OrdersExporter;
use DI\Container;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/../config/settings.php';

$container = new Container();

$container->set('settings', static fn () => $settings);

$container->set(PDO::class, static function () use ($settings): PDO {
    return ConnectionFactory::makePdo($settings['db']);
});

$container->set(OrdersExporter::class, static function (Container $c) use ($settings): OrdersExporter {
    $export = $settings['export'];
    return new OrdersExporter(
        $c->get(PDO::class),
        (string) $export['table'],
        (string) $export['primary_key'],
        (int) $export['batch_size']
    );
});

$container->set(HomeAction::class, static function (Container $c) use ($settings): HomeAction {
    return new HomeAction(
        $c->get(OrdersExporter::class),
        __DIR__ . '/../templates',
        (int) $settings['export']['preview_limit']
    );
});

$container->set(ExportAction::class, static function (Container $c): ExportAction {
    return new ExportAction($c->get(OrdersExporter::class));
});

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addErrorMiddleware((bool) $settings['displayErrorDetails'], true, true);

$app->get('/', HomeAction::class);
$app->get('/export', ExportAction::class);

$app->run();

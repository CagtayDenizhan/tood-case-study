<?php
require_once __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use App\Database\Database;
use App\Services\TodoService;
use App\Services\CategoryService;
use App\Repositories\TodoRepository;
use App\Repositories\CategoryRepository;
use App\Validation\Validator;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$container = new Container();

$container->set('EntityManager', function () {
    return Database::getEntityManager();
});

$container->set('TodoRepository', function () {
    return new TodoRepository();
});

$container->set('CategoryRepository', function () {
    return new CategoryRepository();
});

$container->set('Validator', function () {
    return new Validator();
});

$container->set('TodoService', function ($container) {
    return new TodoService(
        $container->get('TodoRepository'),
        $container->get('Validator'),
        $container->get('EntityManager')
    );
});

$container->set('CategoryService', function ($container) {
    return new CategoryService(
        $container->get('CategoryRepository'),
        $container->get('Validator'),
        $container->get('EntityManager')
    );
});

set_exception_handler(['App\Exceptions\ExceptionHandler', 'handle']);
?>
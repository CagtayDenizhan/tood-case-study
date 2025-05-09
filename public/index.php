<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/temp/debug.log'); // Logları dosyaya yönlendir

error_log("API isteği alındı: " . $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI']);

require_once '../vendor/autoload.php';

use FastRoute\Dispatcher;
use App\Helpers\Response;
use App\Database\Database;
use App\Services\TodoService;
use App\Services\CategoryService;
use App\Repositories\TodoRepository;
use App\Repositories\CategoryRepository;
use App\Validation\Validator;
use Doctrine\ORM\EntityManager;

// CORS ayarları
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    error_log("OPTIONS isteği alındı, çıkış yapılıyor");
    Response::json([], 200);
    exit;
}

try {
    error_log("Doctrine EntityManager başlatılıyor");
    $entityManager = Database::getEntityManager();
    error_log("Doctrine EntityManager başlatıldı");
} catch (\Exception $e) {
    error_log("Doctrine başlatma hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
    Response::json([
        'status' => 'error',
        'message' => 'Veritabanı bağlantı hatası: ' . $e->getMessage(),
        'errors' => []
    ], 500);
    exit;
}

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    // Todo rotaları
    $r->addRoute('GET', '/api/todos', ['App\Controllers\TodoController', 'index']);
    $r->addRoute('GET', '/api/todos/{id:\d+}', ['App\Controllers\TodoController', 'show']);
    $r->addRoute('GET', '/api/todos/{id:\d+}/', ['App\Controllers\TodoController', 'show']);
    $r->addRoute('POST', '/api/todos', ['App\Controllers\TodoController', 'store']);
    $r->addRoute('PUT', '/api/todos/{id:\d+}', ['App\Controllers\TodoController', 'update']);
    $r->addRoute('PUT', '/api/todos/{id:\d+}/', ['App\Controllers\TodoController', 'update']);
    $r->addRoute('PATCH', '/api/todos/{id:\d+}/status', ['App\Controllers\TodoController', 'updateStatus']);
    $r->addRoute('PATCH', '/api/todos/{id:\d+}/status/', ['App\Controllers\TodoController', 'updateStatus']);
    $r->addRoute('DELETE', '/api/todos/{id:\d+}', ['App\Controllers\TodoController', 'destroy']);
    $r->addRoute('DELETE', '/api/todos/{id:\d+}/', ['App\Controllers\TodoController', 'destroy']);
    $r->addRoute('GET', '/api/todos/search', ['App\Controllers\TodoController', 'search']);
    
    // Category rotaları
    $r->addRoute('GET', '/api/categories', ['App\Controllers\CategoryController', 'index']);
    $r->addRoute('POST', '/api/categories', ['App\Controllers\CategoryController', 'store']);
    $r->addRoute('GET', '/api/categories/{id:\d+}', ['App\Controllers\CategoryController', 'show']);
    $r->addRoute('GET', '/api/categories/{id:\d+}/', ['App\Controllers\CategoryController', 'show']);
    $r->addRoute('PUT', '/api/categories/{id:\d+}', ['App\Controllers\CategoryController', 'update']);
    $r->addRoute('PUT', '/api/categories/{id:\d+}/', ['App\Controllers\CategoryController', 'update']);
    $r->addRoute('DELETE', '/api/categories/{id:\d+}', ['App\Controllers\CategoryController', 'destroy']);
    $r->addRoute('DELETE', '/api/categories/{id:\d+}/', ['App\Controllers\CategoryController', 'destroy']);
    $r->addRoute('GET', '/api/categories/{id:\d+}/todos', ['App\Controllers\CategoryController', 'getTodos']);
    $r->addRoute('GET', '/api/categories/{id:\d+}/todos/', ['App\Controllers\CategoryController', 'getTodos']);
    
    // Stats rotaları
    $r->addRoute('GET', '/api/stats/todos', ['App\Controllers\StatsController', 'todoStats']);
    $r->addRoute('GET', '/api/stats/priorities', ['App\Controllers\StatsController', 'priorityStats']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

error_log("Yönlendirme başlıyor: $httpMethod $uri");

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        error_log("404: Uç nokta bulunamadı: $uri");
        Response::json(['status' => 'error', 'message' => 'Uç nokta bulunamadı', 'errors' => ['Uç nokta bulunamadı']], 404);
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        error_log("405: İzin verilmeyen yöntem: $httpMethod");
        Response::json(['status' => 'error', 'message' => 'İzin verilmeyen yöntem', 'errors' => ['Method desteklenmiyor']], 405);
        break;
    case Dispatcher::FOUND:
        error_log("Rota bulundu: " . json_encode($routeInfo));
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        [$controller, $method] = $handler;
        try {
            error_log("DI konteyneri başlatılıyor");
            $container = new \DI\Container();
            $container->set(EntityManager::class, $entityManager);
            $container->set(TodoRepository::class, function () use ($entityManager) {
                return new TodoRepository();
            });
            $container->set(CategoryRepository::class, function () use ($entityManager) {
                return new CategoryRepository();
            });
            $container->set(Validator::class, function () {
                return new Validator();
            });
            $container->set(TodoService::class, function ($c) use ($entityManager) {
                return new TodoService(
                    $c->get(TodoRepository::class),
                    $c->get(Validator::class),
                    $entityManager
                );
            });
            $container->set(CategoryService::class, function ($c) use ($entityManager) {
                return new CategoryService(
                    $c->get(CategoryRepository::class),
                    $c->get(Validator::class),
                    $entityManager
                );
            });
            error_log("DI konteyneri başlatıldı, kontrolör çözümleniyor: $controller");
            $controllerInstance = $container->get($controller);
            error_log("Kontrolör çağırılıyor: $controller::$method");
            $response = $controllerInstance->$method(...array_values($vars));
            error_log("Kontrolör yanıtı: " . json_encode($response));
            Response::json($response);
        } catch (\Throwable $e) {
            error_log("Yönlendirme hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            Response::json([
                'status' => 'error',
                'message' => 'Sunucu hatası: ' . $e->getMessage(),
                'errors' => [],
                'exception_trace' => $e->getTraceAsString()
            ], 500);
        }
        break;
}
?>
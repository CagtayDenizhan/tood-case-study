<?php
namespace App\Controllers;

use App\Services\TodoService;
use Exception;

class TodoController {
    private $todoService;

    public function __construct(TodoService $todoService) {
        error_log("TodoController oluşturuldu");
        $this->todoService = $todoService;
    }

    public function index() {
        try {
            error_log("index metodu çağrıldı");
            $params = [
                'status' => filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS),
                'priority' => filter_input(INPUT_GET, 'priority', FILTER_SANITIZE_SPECIAL_CHARS),
                'sort' => filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'created_at',
                'order' => filter_input(INPUT_GET, 'order', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'desc',
                'page' => filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1,
                'limit' => filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?? 10,
            ];

            $result = $this->todoService->getAllTodos($params);
            $this->sendResponse($result);
        } catch (Exception $e) {
            error_log("index hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            $this->sendResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], $e->getCode() ?: 500);
        }
    }

    public function store() {
        try {
            error_log("store metodu çağrıldı");
            $rawInput = file_get_contents('php://input');
            error_log("Gelen ham veri: " . $rawInput);

            $data = json_decode($rawInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Geçersiz JSON formatı: ' . json_last_error_msg(), 400);
            }
            error_log("Parse edilen veri: " . json_encode($data, JSON_PRETTY_PRINT));

            $sanitizedData = [
                'title' => filter_var($data['title'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
                'description' => filter_var($data['description'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS),
                'status' => filter_var($data['status'] ?? 'pending', FILTER_SANITIZE_SPECIAL_CHARS),
                'priority' => filter_var($data['priority'] ?? 'medium', FILTER_SANITIZE_SPECIAL_CHARS),
                'due_date' => filter_var($data['due_date'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS),
                'category_ids' => array_map('intval', (array)($data['category_ids'] ?? [])),
            ];
            error_log("Temizlenmiş veri: " . json_encode($sanitizedData, JSON_PRETTY_PRINT));

            $result = $this->todoService->createTodo($sanitizedData);
            error_log("TodoService sonucu: " . json_encode($result, JSON_PRETTY_PRINT));

            $this->sendResponse($result, 201);
        } catch (Exception $e) {
            error_log("store hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            $this->sendResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], $e->getCode() ?: 500);
        }
    }

    public function show($id) {
        try {
            error_log("show metodu çağrıldı, id=$id");
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception('Geçersiz ID', 400);
            }

            $result = $this->todoService->getTodoById($id);
            $this->sendResponse($result);
        } catch (Exception $e) {
            error_log("show hatası: " . $e->getMessage());
            $this->sendResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], $e->getCode() ?: 500);
        }
    }

    public function update($id) {
        try {
            error_log("update metodu çağrıldı, id=$id");
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception('Geçersiz ID', 400);
            }

            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Geçersiz JSON formatı', 400);
            }

            $sanitizedData = [
                'title' => filter_var($data['title'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
                'description' => filter_var($data['description'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS),
                'status' => filter_var($data['status'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS),
                'priority' => filter_var($data['priority'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS),
                'due_date' => filter_var($data['due_date'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS),
                'category_ids' => array_map('intval', (array)($data['category_ids'] ?? [])),
            ];

            $result = $this->todoService->updateTodo($id, $sanitizedData);
            $this->sendResponse($result);
        } catch (Exception $e) {
            error_log("update hatası: " . $e->getMessage());
            $this->sendResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], $e->getCode() ?: 500);
        }
    }

    public function updateStatus($id) {
        try {
            error_log("updateStatus metodu çağrıldı, id=$id");
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception('Geçersiz ID', 400);
            }

            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Geçersiz JSON formatı', 400);
            }

            $sanitizedData = [
                'status' => filter_var($data['status'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            ];

            $result = $this->todoService->updateTodoStatus($id, $sanitizedData);
            $this->sendResponse($result);
        } catch (Exception $e) {
            error_log("updateStatus hatası: " . $e->getMessage());
            $this->sendResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], $e->getCode() ?: 500);
        }
    }

    public function destroy($id) {
        try {
            error_log("destroy metodu çağrıldı, id=$id");
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception('Geçersiz ID', 400);
            }

            $result = $this->todoService->destroy($id);
            $this->sendResponse($result);
        } catch (Exception $e) {
            error_log("destroy hatası: " . $e->getMessage());
            $this->sendResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], $e->getCode() ?: 500);
        }
    }

    public function search() {
        try {
            error_log("search metodu çağrıldı");
            $query = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS);
            $params = [
                'page' => filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1,
                'limit' => filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?? 10,
            ];

            if (empty($query)) {
                throw new Exception('Arama terimi gerekli', 400);
            }

            $result = $this->todoService->searchTodos($query, $params);
            $this->sendResponse($result);
        } catch (Exception $e) {
            error_log("search hatası: " . $e->getMessage());
            $this->sendResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], $e->getCode() ?: 500);
        }
    }

    private function sendResponse(array $data, int $statusCode = 200) {
        header('Content-Type: application/json; charset=utf-8', true, $statusCode);
        echo json_encode($data);
        exit;
    }
}
?>
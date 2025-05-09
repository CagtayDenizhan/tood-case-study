<?php

namespace App\Controllers;

use App\Services\CategoryService;
use App\Helpers\Response;
use Exception;

class CategoryController
{
    private $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        try {
            // Pagination parametrelerini bir dizi olarak hazırla
            $params = [
                'page' => isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1,
                'limit' => isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 10,
            ];
            $params['offset'] = ($params['page'] - 1) * $params['limit'];

            // CategoryService::getAllCategories metodunu çağır
            $response = $this->categoryService->getAllCategories($params);

            // Yanıtı döndür
            Response::json($response, 200);
        } catch (Exception $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $e->getCode() ?: 500);
        }
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::json([
                'status' => 'error',
                'message' => 'Geçersiz JSON formatı: ' . json_last_error_msg(),
                'errors' => ['Geçersiz JSON formatı']
            ], 400);
        }

        try {
            $response = $this->categoryService->createCategory($data);
            Response::json($response, 201);
        } catch (Exception $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $e->getCode() ?: 400);
        }
    }

    public function show($id)
    {
        try {
            $response = $this->categoryService->getCategoryById($id);
            Response::json($response, 200);
        } catch (Exception $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $e->getCode() ?: 404);
        }
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::json([
                'status' => 'error',
                'message' => 'Geçersiz JSON formatı: ' . json_last_error_msg(),
                'errors' => ['Geçersiz JSON formatı']
            ], 400);
        }

        try {
            $response = $this->categoryService->updateCategory($id, $data);
            Response::json($response, 200);
        } catch (Exception $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $e->getCode() ?: 400);
        }
    }

    public function destroy($id)
    {
        try {
            $response = $this->categoryService->deleteCategory($id);
            Response::json($response, 200); // 204 yerine 200, çünkü yanıt gövdesi var
        } catch (Exception $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $e->getCode() ?: 400);
        }
    }

    public function getTodos($id)
    {
        try {
            // Pagination parametrelerini bir dizi olarak hazırla
            $params = [
                'page' => isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1,
                'limit' => isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 10,
            ];
            $params['offset'] = ($params['page'] - 1) * $params['limit'];

            $response = $this->categoryService->getTodosByCategory($id, $params);
            Response::json($response, 200);
        } catch (Exception $e) {
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $e->getCode() ?: 400);
        }
    }
}
?>
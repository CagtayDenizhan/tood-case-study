<?php
namespace App\Controllers;

use App\Services\TodoService;
use App\Helpers\Response;
use Exception;

class StatsController
{
    private $todoService;

    public function __construct(TodoService $todoService)
    {
        error_log("StatsController oluşturuldu");
        $this->todoService = $todoService;
    }

    public function todoStats()
    {
        try {
            error_log("todoStats metodu çağrıldı");
            $result = $this->todoService->getTodoStats();
            Response::json($result, 200);
        } catch (Exception $e) {
            error_log("todoStats hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $e->getCode() ?: 500);
        }
    }

    public function priorityStats()
    {
        try {
            error_log("priorityStats metodu çağrıldı");
            $result = $this->todoService->getPriorityStats();
            Response::json($result, 200);
        } catch (Exception $e) {
            error_log("priorityStats hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            Response::json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], $e->getCode() ?: 500);
        }
    }
}
?>
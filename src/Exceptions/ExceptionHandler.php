<?php
namespace App\Exceptions;

use Exception;

class ExceptionHandler
{
    public static function handle(Exception $e)
    {
        $code = $e->getCode() ?: 500;
        $response = [
            'status' => 'error',
            'message' => $e->getMessage(),
            'errors' => [$e->getMessage()],
            'code' => $code
        ];

        if (env('APP_ENV') !== 'production') {
            $response['trace'] = $e->getTraceAsString();
        }

        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        exit;
    }
}
?>
<?php
namespace App\Helpers;

class Response {
    public static function json(array $data, int $statusCode = 200): void {
        error_log("Response::json çağrıldı, veri: " . json_encode($data) . ", durum kodu: $statusCode");
        header('Content-Type: application/json', true, $statusCode);
        echo json_encode($data);
        exit;
    }
}
?>
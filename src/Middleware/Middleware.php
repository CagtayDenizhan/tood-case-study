<?php
namespace App\Middleware;

class Middleware
{
    public static function handleCors()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }

    public static function handleRateLimit()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $cacheFile = sys_get_temp_dir() . '/rate_limit_' . md5($ip) . '.json';
        $limit = 100; // 1 saatte 100 istek
        $window = 3600; // 1 saat

        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            $requests = $data['requests'] ?? [];
            $requests = array_filter($requests, function ($timestamp) use ($window) {
                return $timestamp > time() - $window;
            });

            if (count($requests) >= $limit) {
                http_response_code(429);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Çok fazla istek yapıldı',
                    'errors' => ['Rate limit aşıldı']
                ]);
                exit;
            }

            $requests[] = time();
        } else {
            $requests = [time()];
        }

        file_put_contents($cacheFile, json_encode(['requests' => $requests]));
    }
}
?>
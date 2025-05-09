<?php
namespace App\Database;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class Database {
    private static $entityManager = null;

    public static function getEntityManager(): EntityManager {
        if (self::$entityManager !== null) {
            return self::$entityManager;
        }

        $config = require __DIR__ . '/../../config/database.php';
        $paths = [__DIR__ . '/../Models'];
        $isDevMode = true;

        $dbParams = [
            'driver'   => $config['driver'],
            'host'     => $config['host'],
            'dbname'   => $config['dbname'],
            'user'     => $config['user'],
            'password' => $config['password'],
            'charset'  => $config['charset'],
        ];

        try {
            $doctrineConfig = Setup::createAnnotationMetadataConfiguration(
                $paths,
                $isDevMode,
                null,
                null,
                false // <-- Burada simple annotation reader devre dışı
            );

            self::$entityManager = EntityManager::create($dbParams, $doctrineConfig);
        } catch (\Exception $e) {
            error_log("EntityManager oluşturma hatası: " . $e->getMessage());
            throw new \Exception("Veritabanı bağlantı hatası: " . $e->getMessage());
        }

        return self::$entityManager;
    }
}
?>

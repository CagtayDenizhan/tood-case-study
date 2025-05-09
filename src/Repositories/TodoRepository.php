<?php
namespace App\Repositories;

use App\Models\Todo;
use App\Database\Database;
use Doctrine\ORM\EntityManager;

class TodoRepository {
    private $entityManager;

    public function __construct() {
        $this->entityManager = Database::getEntityManager();
        error_log("TodoRepository oluşturuldu");
    }

    public function findAll(array $params): array {
        try {
            $qb = $this->entityManager->createQueryBuilder();
            $qb->select('t')
               ->from(Todo::class, 't')
               ->where('t.deleted_at IS NULL');

            // Filtreleme
            if (!empty($params['status'])) {
                $qb->andWhere('t.status = :status')
                   ->setParameter('status', $params['status']);
            }
            if (!empty($params['priority'])) {
                $qb->andWhere('t.priority = :priority')
                   ->setParameter('priority', $params['priority']);
            }

            // Sıralama
            $sort = $params['sort'] ?? 'created_at';
            $order = $params['order'] ?? 'desc';
            $qb->orderBy("t.$sort", $order);

            // Sayfalama
            $page = max(1, $params['page'] ?? 1);
            $limit = min(50, $params['limit'] ?? 10);
            $qb->setFirstResult(($page - 1) * $limit)
               ->setMaxResults($limit);

            $query = $qb->getQuery();
            $todos = $query->getResult();

            // Toplam sayıyı hesapla
            $qbCount = $this->entityManager->createQueryBuilder();
            $qbCount->select('COUNT(t.id)')
                    ->from(Todo::class, 't')
                    ->where('t.deleted_at IS NULL');
            if (!empty($params['status'])) {
                $qbCount->andWhere('t.status = :status')
                        ->setParameter('status', $params['status']);
            }
            if (!empty($params['priority'])) {
                $qbCount->andWhere('t.priority = :priority')
                        ->setParameter('priority', $params['priority']);
            }
            $total = $qbCount->getQuery()->getSingleScalarResult();

            return [
                'todos' => $todos,
                'total' => $total,
                'page' => $page,
                'limit' => $limit
            ];
        } catch (\Exception $e) {
            error_log("findAll hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function findById(int $id): ?Todo {
        try {
            return $this->entityManager->getRepository(Todo::class)->findOneBy([
                'id' => $id,
                'deleted_at' => null
            ]);
        } catch (\Exception $e) {
            error_log("findById hatası: " . $e->getMessage());
            throw $e;
        }
    }

    public function search(string $query, int $page = 1, int $limit = 10): array {
        try {
            $qb = $this->entityManager->createQueryBuilder();
            $qb->select('t')
               ->from(Todo::class, 't')
               ->where('t.deleted_at IS NULL')
               ->andWhere('t.title LIKE :query OR t.description LIKE :query')
               ->setParameter('query', '%' . $query . '%')
               ->orderBy('t.created_at', 'DESC')
               ->setFirstResult(($page - 1) * $limit)
               ->setMaxResults($limit);

            $todos = $qb->getQuery()->getResult();

            $qbCount = $this->entityManager->createQueryBuilder();
            $qbCount->select('COUNT(t.id)')
                    ->from(Todo::class, 't')
                    ->where('t.deleted_at IS NULL')
                    ->andWhere('t.title LIKE :query OR t.description LIKE :query')
                    ->setParameter('query', '%' . $query . '%');
            $total = $qbCount->getQuery()->getSingleScalarResult();

            return [
                'todos' => $todos,
                'total' => $total,
                'page' => $page,
                'limit' => $limit
            ];
        } catch (\Exception $e) {
            error_log("search hatası: " . $e->getMessage());
            throw $e;
        }
    }

    public function save(Todo $todo): void {
        try {
            error_log("TodoRepository: Todo persist ediliyor, ID: " . ($todo->getId() ?? 'yok'));
            $this->entityManager->persist($todo);
            error_log("TodoRepository: Todo flush ediliyor");
            $this->entityManager->flush();
            error_log("TodoRepository: Todo kaydedildi, ID: " . $todo->getId());
        } catch (\Exception $e) {
            error_log("TodoRepository save hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            throw new \Exception("Veritabanına kaydedilemedi: " . $e->getMessage(), 500);
        }
    }

    public function delete(Todo $todo): void {
        try {
            $todo->setDeletedAt(new \DateTime());
            $this->entityManager->persist($todo);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            error_log("delete hatası: " . $e->getMessage());
            throw $e;
        }
    }
}
?>
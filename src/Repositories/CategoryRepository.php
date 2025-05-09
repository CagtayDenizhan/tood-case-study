<?php
namespace App\Repositories;

use App\Models\Category;
use App\Database\Database;

class CategoryRepository
{
    private $entityManager;

    public function __construct()
    {
        $this->entityManager = Database::getEntityManager();
        error_log("CategoryRepository oluşturuldu");
    }

    public function findAll(array $params): array
    {
        try {
            $qb = $this->entityManager->createQueryBuilder();
            $qb->select('c')
               ->from(Category::class, 'c')
               ->orderBy('c.created_at', 'DESC');

            $page = max(1, $params['page'] ?? 1);
            $limit = min(50, $params['limit'] ?? 10);
            $qb->setFirstResult(($page - 1) * $limit)
               ->setMaxResults($limit);

            $query = $qb->getQuery();
            $categories = $query->getResult();

            $qbCount = $this->entityManager->createQueryBuilder();
            $qbCount->select('COUNT(c.id)')
                    ->from(Category::class, 'c');
            $total = $qbCount->getQuery()->getSingleScalarResult();

            return [
                'categories' => $categories,
                'total' => $total,
                'page' => $page,
                'limit' => $limit
            ];
        } catch (\Exception $e) {
            error_log("findAll hatası: " . $e->getMessage());
            throw $e;
        }
    }

    public function findById(int $id): ?Category
    {
        try {
            return $this->entityManager->getRepository(Category::class)->find($id);
        } catch (\Exception $e) {
            error_log("findById hatası: " . $e->getMessage());
            throw $e;
        }
    }

    public function findTodosByCategory(int $categoryId, array $params): array
    {
        try {
            $qb = $this->entityManager->createQueryBuilder();
            $qb->select('t')
               ->from('App\Models\Todo', 't')
               ->join('t.categories', 'c')
               ->where('c.id = :categoryId')
               ->andWhere('t.deleted_at IS NULL')
               ->setParameter('categoryId', $categoryId)
               ->orderBy('t.created_at', 'DESC');

            $page = max(1, $params['page'] ?? 1);
            $limit = min(50, $params['limit'] ?? 10);
            $qb->setFirstResult(($page - 1) * $limit)
               ->setMaxResults($limit);

            $todos = $qb->getQuery()->getResult();

            $qbCount = $this->entityManager->createQueryBuilder();
            $qbCount->select('COUNT(t.id)')
                    ->from('App\Models\Todo', 't')
                    ->join('t.categories', 'c')
                    ->where('c.id = :categoryId')
                    ->andWhere('t.deleted_at IS NULL')
                    ->setParameter('categoryId', $categoryId);
            $total = $qbCount->getQuery()->getSingleScalarResult();

            return [
                'todos' => $todos,
                'total' => $total,
                'page' => $page,
                'limit' => $limit
            ];
        } catch (\Exception $e) {
            error_log("findTodosByCategory hatası: " . $e->getMessage());
            throw $e;
        }
    }
}
?>
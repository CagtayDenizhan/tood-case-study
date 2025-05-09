<?php
namespace App\Services;

use App\Models\Todo;
use App\Models\Category;
use App\Repositories\TodoRepository;
use App\Validation\Validator;
use Doctrine\ORM\EntityManager;

class TodoService
{
    private $todoRepository;
    private $validator;
    private $entityManager;

    public function __construct(TodoRepository $todoRepository, Validator $validator, EntityManager $entityManager)
    {
        error_log("TodoService oluşturuldu");
        $this->todoRepository = $todoRepository;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    public function getAllTodos(array $params): array
    {
        try {
            error_log("getAllTodos çağrıldı: " . json_encode($params));
            $result = $this->todoRepository->findAll($params);
            $todos = array_map(function ($todo) {
                return $this->formatTodo($todo);
            }, $result['todos']);

            return [
                'status' => 'success',
                'data' => $todos,
                'meta' => [
                    'pagination' => [
                        'total' => $result['total'],
                        'per_page' => $result['limit'],
                        'current_page' => $result['page'],
                        'last_page' => ceil($result['total'] / $result['limit']),
                        'from' => ($result['page'] - 1) * $result['limit'] + 1,
                        'to' => min($result['page'] * $result['limit'], $result['total'])
                    ]
                ]
            ];
        } catch (\Exception $e) {
            error_log("getAllTodos hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            throw new \Exception('Todos alınamadı: ' . $e->getMessage(), 500);
        }
    }

    public function getTodoById(int $id): array
    {
        try {
            error_log("getTodoById çağrıldı: id=$id");
            $todo = $this->todoRepository->findById($id);
            if (!$todo) {
                throw new \Exception('Todo bulunamadı', 404);
            }
            return [
                'status' => 'success',
                'data' => $this->formatTodo($todo)
            ];
        } catch (\Exception $e) {
            error_log("getTodoById hatası: " . $e->getMessage());
            throw $e;
        }
    }

    public function createTodo(array $data): array
    {
        try {
            error_log("createTodo başladı: " . json_encode($data, JSON_PRETTY_PRINT));

            $rules = [
                'title' => 'required|string|min:3|max:255',
                'description' => 'string|max:500|nullable',
                'status' => 'in:pending,in_progress,completed,cancelled|nullable',
                'priority' => 'in:low,medium,high|nullable',
                'due_date' => 'date|after:now|nullable',
                'category_ids' => 'array|array_of_integers|nullable'
            ];

            error_log("Doğrulama başlıyor");
            $validationResult = $this->validator->validate($data, $rules);
            if (!empty($validationResult)) {
                error_log("Doğrulama hataları: " . json_encode($validationResult, JSON_PRETTY_PRINT));
                throw new \Exception(json_encode($validationResult), 422);
            }
            error_log("Doğrulama geçti");

            $todo = new Todo();
            $todo->setTitle($data['title']);
            $todo->setDescription($data['description'] ?? null);
            $todo->setStatus($data['status'] ?? 'pending');
            $todo->setPriority($data['priority'] ?? 'medium');
            if (!empty($data['due_date'])) {
                error_log("due_date ayarlanıyor: " . $data['due_date']);
                try {
                    $dueDate = new \DateTime($data['due_date']);
                    $todo->setDueDate($dueDate);
                } catch (\Exception $e) {
                    error_log("Geçersiz due_date formatı: " . $e->getMessage());
                    throw new \Exception("Geçersiz tarih formatı: " . $data['due_date'], 400);
                }
            } else {
                $todo->setDueDate(null);
            }

            if (!empty($data['category_ids'])) {
                error_log("Kategoriler işleniyor: " . json_encode($data['category_ids']));
                $categoryRepo = $this->entityManager->getRepository(Category::class);
                foreach ($data['category_ids'] as $categoryId) {
                    $category = $categoryRepo->find($categoryId);
                    if (!$category) {
                        error_log("Kategori bulunamadı: ID $categoryId");
                        throw new \Exception("Kategori ID $categoryId bulunamadı", 400);
                    }
                    $todo->addCategory($category);
                }
            }

            error_log("Todo persist ediliyor");
            $this->entityManager->persist($todo);
            error_log("Todo flush ediliyor");
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                error_log("Flush hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
                throw new \Exception("Veritabanına kaydedilemedi: " . $e->getMessage(), 500);
            }
            error_log("Todo kaydedildi, ID: " . $todo->getId());

            $result = [
                'status' => 'success',
                'message' => 'Todo başarıyla oluşturuldu',
                'data' => $this->formatTodo($todo)
            ];
            error_log("Yanıt oluşturuldu: " . json_encode($result, JSON_PRETTY_PRINT));
            return $result;
        } catch (\Exception $e) {
            error_log("createTodo hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function updateTodo(int $id, array $data): array
    {
        try {
            error_log("updateTodo çağrıldı: id=$id, data=" . json_encode($data));
            $todo = $this->todoRepository->findById($id);
            if (!$todo) {
                throw new \Exception('Todo bulunamadı', 404);
            }

            $rules = [
                'title' => 'required|string|min:3|max:255',
                'description' => 'string|max:500|nullable',
                'status' => 'in:pending,in_progress,completed,cancelled|nullable',
                'priority' => 'in:low,medium,high|nullable',
                'due_date' => 'date|after:now|nullable',
                'category_ids' => 'array|array_of_integers|nullable'
            ];

            $validationResult = $this->validator->validate($data, $rules);
            if (!empty($validationResult)) {
                throw new \Exception(json_encode($validationResult), 422);
            }

            $todo->setTitle($data['title']);
            $todo->setDescription($data['description'] ?? null);
            $todo->setStatus($data['status'] ?? $todo->getStatus());
            $todo->setPriority($data['priority'] ?? $todo->getPriority());
            if (!empty($data['due_date'])) {
                $todo->setDueDate(new \DateTime($data['due_date']));
            } else {
                $todo->setDueDate(null);
            }
            $todo->setUpdatedAt(new \DateTime());

            $todo->getCategories()->clear();
            if (!empty($data['category_ids'])) {
                $categoryRepo = $this->entityManager->getRepository(Category::class);
                foreach ($data['category_ids'] as $categoryId) {
                    $category = $categoryRepo->find($categoryId);
                    if (!$category) {
                        throw new \Exception("Kategori ID $categoryId bulunamadı", 400);
                    }
                    $todo->addCategory($category);
                }
            }

            $this->entityManager->persist($todo);
            $this->entityManager->flush();

            return [
                'status' => 'success',
                'message' => 'Todo başarıyla güncellendi',
                'data' => $this->formatTodo($todo)
            ];
        } catch (\Exception $e) {
            error_log("updateTodo hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function updateTodoStatus(int $id, array $data): array
    {
        try {
            error_log("updateTodoStatus çağrıldı: id=$id, data=" . json_encode($data));
            $todo = $this->todoRepository->findById($id);
            if (!$todo) {
                throw new \Exception('Todo bulunamadı', 404);
            }

            $rules = [
                'status' => 'required|in:pending,in_progress,completed,cancelled'
            ];

            $validationResult = $this->validator->validate($data, $rules);
            if (!empty($validationResult)) {
                throw new \Exception(json_encode($validationResult), 422);
            }

            $todo->setStatus($data['status']);
            $todo->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($todo);
            $this->entityManager->flush();

            return [
                'status' => 'success',
                'message' => 'Todo durumu başarıyla güncellendi',
                'data' => $this->formatTodo($todo)
            ];
        } catch (\Exception $e) {
            error_log("updateTodoStatus hatası: " . $e->getMessage());
            throw $e;
        }
    }

    public function destroy(int $id): array
    {
        try {
            error_log("destroy çağrıldı: id=$id");
            $todo = $this->todoRepository->findById($id);
            if (!$todo) {
                throw new \Exception('Todo bulunamadı', 404);
            }

            $todo->setDeletedAt(new \DateTime());
            $this->entityManager->persist($todo);
            $this->entityManager->flush();

            return [
                'status' => 'success',
                'message' => 'Todo başarıyla silindi'
            ];
        } catch (\Exception $e) {
            error_log("destroy hatası: " . $e->getMessage());
            throw $e;
        }
    }

    public function searchTodos(string $query, array $params): array
    {
        try {
            error_log("searchTodos çağrıldı: query=$query, params=" . json_encode($params));
            $result = $this->todoRepository->search($query, $params['page'] ?? 1, $params['limit'] ?? 10);
            $todos = array_map(function ($todo) {
                return $this->formatTodo($todo);
            }, $result['todos']);

            return [
                'status' => 'success',
                'data' => $todos,
                'meta' => [
                    'pagination' => [
                        'total' => $result['total'],
                        'per_page' => $result['limit'],
                        'current_page' => $result['page'],
                        'last_page' => ceil($result['total'] / $result['limit']),
                        'from' => ($result['page'] - 1) * $result['limit'] + 1,
                        'to' => min($result['page'] * $result['limit'], $result['total'])
                    ]
                ]
            ];
        } catch (\Exception $e) {
            error_log("searchTodos hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function getTodoStats(): array
    {
        try {
            error_log("getTodoStats çağrıldı");
            $qb = $this->entityManager->createQueryBuilder(); // Düzeltildi: СоздатьQueryBuilder -> createQueryBuilder
            $qb->select('t.status, COUNT(t.id) as count')
               ->from(Todo::class, 't')
               ->where('t.deleted_at IS NULL')
               ->groupBy('t.status');
            $results = $qb->getQuery()->getArrayResult();

            $stats = [
                'pending' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'cancelled' => 0,
                'total' => 0
            ];
            foreach ($results as $result) {
                $stats[$result['status']] = (int)$result['count'];
                $stats['total'] += (int)$result['count'];
            }

            $overdue = $this->entityManager->createQueryBuilder()
                ->select('COUNT(t.id)')
                ->from(Todo::class, 't')
                ->where('t.due_date < :now')
                ->andWhere('t.status NOT IN (:completed, :cancelled)')
                ->andWhere('t.deleted_at IS NULL')
                ->setParameter('now', new \DateTime())
                ->setParameter('completed', 'completed')
                ->setParameter('cancelled', 'cancelled')
                ->getQuery()
                ->getSingleScalarResult();

            $stats['overdue'] = (int)$overdue;

            return [
                'status' => 'success',
                'data' => $stats
            ];
        } catch (\Exception $e) {
            error_log("getTodoStats hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            throw new \Exception('Todo istatistikleri alınamadı: ' . $e->getMessage(), 500);
        }
    }

    public function getPriorityStats(): array
    {
        try {
            error_log("getPriorityStats çağrıldı");
            $qb = $this->entityManager->createQueryBuilder();
            $qb->select('t.priority, COUNT(t.id) as count')
               ->from(Todo::class, 't')
               ->where('t.deleted_at IS NULL')
               ->groupBy('t.priority');
            $results = $qb->getQuery()->getArrayResult();

            $stats = [
                'low' => 0,
                'medium' => 0,
                'high' => 0,
                'total' => 0
            ];
            foreach ($results as $result) {
                $stats[$result['priority']] = (int)$result['count'];
                $stats['total'] += (int)$result['count'];
            }

            return [
                'status' => 'success',
                'data' => $stats
            ];
        } catch (\Exception $e) {
            error_log("getPriorityStats hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            throw new \Exception('Öncelik istatistikleri alınamadı: ' . $e->getMessage(), 500);
        }
    }

    private function formatTodo(Todo $todo): array
    {
        return [
            'id' => $todo->getId(),
            'title' => $todo->getTitle(),
            'description' => $todo->getDescription(),
            'status' => $todo->getStatus(),
            'priority' => $todo->getPriority(),
            'due_date' => $todo->getDueDate() ? $todo->getDueDate()->format('Y-m-d\TH:i:s') : null,
            'created_at' => $todo->getCreatedAt()->format('Y-m-d\TH:i:s'),
            'updated_at' => $todo->getUpdatedAt()->format('Y-m-d\TH:i:s'),
            'categories' => array_map(function ($category) {
                return [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'color' => $category->getColor()
                ];
            }, $todo->getCategories()->toArray())
        ];
    }
}
?>
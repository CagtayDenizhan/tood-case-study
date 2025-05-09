<?php
namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Validation\Validator;
use Doctrine\ORM\EntityManager;

class CategoryService
{
    private $categoryRepository;
    private $validator;
    private $entityManager;

    public function __construct(CategoryRepository $categoryRepository, Validator $validator, EntityManager $entityManager)
    {
        error_log("CategoryService oluşturuldu");
        $this->categoryRepository = $categoryRepository;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    public function getAllCategories(array $params): array
    {
        try {
            error_log("getAllCategories çağrıldı: " . json_encode($params));
            $result = $this->categoryRepository->findAll($params);
            $categories = array_map(function ($category) {
                return $this->formatCategory($category);
            }, $result['categories']);

            return [
                'status' => 'success',
                'data' => $categories,
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
            error_log("getAllCategories hatası: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            throw new \Exception('Kategoriler alınamadı: ' . $e->getMessage(), 500);
        }
    }

    public function getCategoryById(int $id): array
    {
        try {
            $category = $this->categoryRepository->findById($id);
            if (!$category) {
                throw new \Exception('Kategori bulunamadı', 404);
            }
            return [
                'status' => 'success',
                'data' => $this->formatCategory($category)
            ];
        } catch (\Exception $e) {
            error_log("getCategoryById hatası: " . $e->getMessage());
            throw $e;
        }
    }

    public function createCategory(array $data): array
    {
        try {
            error_log("createCategory başladı: " . json_encode($data));
            $rules = [
                'name' => 'required|string|min:3|max:100',
                'color' => 'regex:/^#[0-9A-Fa-f]{6}$/'
            ];
            $validationResult = $this->validator->validate($data, $rules);
            if (!empty($validationResult)) {
                throw new \Exception(json_encode($validationResult), 422);
            }

            $category = new Category();
            $category->setName($data['name']);
            $category->setColor($data['color']);
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return [
                'status' => 'success',
                'message' => 'Kategori başarıyla oluşturuldu',
                'data' => $this->formatCategory($category)
            ];
        } catch (\Exception $e) {
            error_log("createCategory hatası: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateCategory(int $id, array $data): array
    {
        try {
            $category = $this->categoryRepository->findById($id);
            if (!$category) {
                throw new \Exception('Kategori bulunamadı', 404);
            }

            $rules = [
                'name' => 'required|string|min:3|max:100',
                'color' => 'regex:/^#[0-9A-Fa-f]{6}$/'
            ];
            $validationResult = $this->validator->validate($data, $rules);
            if (!empty($validationResult)) {
                throw new \Exception(json_encode($validationResult), 422);
            }

            $category->setName($data['name']);
            $category->setColor($data['color']);
            $category->setUpdatedAt(new \DateTime());
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return [
                'status' => 'success',
                'message' => 'Kategori başarıyla güncellendi',
                'data' => $this->formatCategory($category)
            ];
        } catch (\Exception $e) {
            error_log("updateCategory hatası: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteCategory(int $id): array
    {
        try {
            $category = $this->categoryRepository->findById($id);
            if (!$category) {
                throw new \Exception('Kategori bulunamadı', 404);
            }

            $this->entityManager->remove($category);
            $this->entityManager->flush();

            return [
                'status' => 'success',
                'message' => 'Kategori başarıyla silindi'
            ];
        } catch (\Exception $e) {
            error_log("deleteCategory hatası: " . $e->getMessage());
            throw $e;
        }
    }

    public function getTodosByCategory(int $id, array $params): array
    {
        try {
            error_log("getTodosByCategory çağrıldı: id=$id, params=" . json_encode($params));
            $category = $this->categoryRepository->findById($id);
            if (!$category) {
                throw new \Exception('Kategori bulunamadı', 404);
            }
            $result = $this->categoryRepository->findTodosByCategory($id, $params);
            $todos = array_map(function ($todo) {
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
            error_log("getTodosByCategory hatası: " . $e->getMessage());
            throw $e;
        }
    }

    private function formatCategory(Category $category): array
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'color' => $category->getColor(),
            'created_at' => $category->getCreatedAt()->format('Y-m-d\TH:i:s'),
            'updated_at' => $category->getUpdatedAt()->format('Y-m-d\TH:i:s')
        ];
    }
}
?>
<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\TodoService;
use App\Repositories\TodoRepository;
use App\Validation\Validator;
use Doctrine\ORM\EntityManager;

class TodoServiceTest extends TestCase
{
    private $todoRepository;
    private $validator;
    private $entityManager;
    private $todoService;

    protected function setUp(): void
    {
        $this->todoRepository = $this->createMock(TodoRepository::class);
        $this->validator = $this->createMock(Validator::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->todoService = new TodoService($this->todoRepository, $this->validator, $this->entityManager);
    }

    public function testCreateTodoValidationFails()
    {
        $data = ['title' => 'ab']; // Çok kısa başlık
        $this->validator->method('validate')->willReturn(['title' => 'Başlık en az 3 karakter olmalı']);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(422);
        $this->todoService->createTodo($data);
    }
}
?>
<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity
 * @ORM\Table(name="todos")
 */
class Todo {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('pending','in_progress','completed','cancelled') DEFAULT 'pending'")
     */
    private $status = 'pending';

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('low','medium','high') DEFAULT 'medium'")
     */
    private $priority = 'medium';

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $due_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleted_at;

    /**
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="todos")
     * @ORM\JoinTable(name="todo_category",
     *      joinColumns={@ORM\JoinColumn(name="todo_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $categories;

    public function __construct() {
        $this->categories = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    // Getters ve Setters
    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    public function getStatus(): ?string {
        return $this->status;
    }

    public function setStatus(string $status): self {
        $this->status = $status;
        return $this;
    }

    public function getPriority(): ?string {
        return $this->priority;
    }

    public function setPriority(string $priority): self {
        $this->priority = $priority;
        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface {
        return $this->due_date;
    }

    public function setDueDate(?\DateTimeInterface $due_date): self {
        $this->due_date = $due_date;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeInterface $deleted_at): self {
        $this->deleted_at = $deleted_at;
        return $this;
    }

    public function getCategories(): Collection {
        return $this->categories;
    }

    public function addCategory(Category $category): self {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }
        return $this;
    }

    public function removeCategory(Category $category): self {
        $this->categories->removeElement($category);
        return $this;
    }
}
?>
<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 */
class Category {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=7, options={"default"="#FFFFFF"})
     */
    private $color = '#FFFFFF';

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\ManyToMany(targetEntity="Todo", mappedBy="categories")
     */
    private $todos;

    public function __construct() {
        $this->todos = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    // Getters ve Setters
    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getColor(): ?string {
        return $this->color;
    }

    public function setColor(string $color): self {
        $this->color = $color;
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

    public function getTodos(): Collection {
        return $this->todos;
    }
}
?>
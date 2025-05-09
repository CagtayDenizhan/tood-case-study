<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202505080001_CreateTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create todos, categories, and todo_categories tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE todos (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                status ENUM("pending","in_progress","completed","cancelled") DEFAULT "pending",
                priority ENUM("low","medium","high") DEFAULT "medium",
                due_date DATETIME,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ');

        $this->addSql('
            CREATE TABLE categories (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                color VARCHAR(7) DEFAULT "#FFFFFF",
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ');

        $this->addSql('
            CREATE TABLE todo_categories (
                todo_id INT UNSIGNED NOT NULL,
                category_id INT UNSIGNED NOT NULL,
                PRIMARY KEY (todo_id, category_id),
                FOREIGN KEY (todo_id) REFERENCES todos(id) ON DELETE CASCADE,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE todo_categories');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE todos');
    }
}
?>
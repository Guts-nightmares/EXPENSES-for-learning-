<?php

require_once __DIR__ . '/../Database.php';

class Expense
{
    private $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM expenses ORDER BY date DESC";
        $result = $this->pdo->query($sql);
        return $result->fetchAll();
    }

    public function create($desc, $amount, $cat)
    {
        $sql = "INSERT INTO expenses (description, amount, category, date) VALUES (?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$desc, $amount, $cat]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM expenses WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getTotalByCategory()
    {
        $sql = "SELECT category, SUM(amount) as total FROM expenses GROUP BY category";
        $result = $this->pdo->query($sql);
        return $result->fetchAll();
    }

    public function getMonthlyTotal()
    {
        $sql = "SELECT SUM(amount) as total FROM expenses WHERE MONTH(date) = MONTH(NOW())";
        $result = $this->pdo->query($sql);
        return $result->fetch();
    }
}

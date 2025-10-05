<?php
require_once __DIR__ . '/Database.php';

class Restaurant {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("
            SELECT 
                id,
                name,
                description,
                image,
                category,
                rating,
                delivery_time,
                delivery_fee,
                CASE 
                    WHEN opening_hours->>'$.open' <= TIME_FORMAT(NOW(), '%H:%i:%s') 
                    AND opening_hours->>'$.close' > TIME_FORMAT(NOW(), '%H:%i:%s') 
                    THEN 1 
                    ELSE 0 
                END as is_open
            FROM restaurants 
            ORDER BY rating DESC, is_open DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM restaurants WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function search($term, $category = null) {
        $sql = "
            SELECT 
                id,
                name,
                description,
                image,
                category,
                rating,
                delivery_time,
                delivery_fee,
                CASE 
                    WHEN opening_hours->>'$.open' <= TIME_FORMAT(NOW(), '%H:%i:%s') 
                    AND opening_hours->>'$.close' > TIME_FORMAT(NOW(), '%H:%i:%s') 
                    THEN 1 
                    ELSE 0 
                END as is_open
            FROM restaurants 
            WHERE 1=1
        ";
        $params = [];

        if ($term) {
            $sql .= " AND (name LIKE ? OR description LIKE ? OR category LIKE ?)";
            $term = "%$term%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if ($category && $category !== 'Todos') {
            $sql .= " AND category = ?";
            $params[] = $category;
        }

        $sql .= " ORDER BY rating DESC, is_open DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategories() {
        $stmt = $this->db->query("SELECT DISTINCT category FROM restaurants");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getMenu($restaurantId) {
        $stmt = $this->db->prepare("
            SELECT * FROM menu_items 
            WHERE restaurant_id = ? 
            ORDER BY category, name
        ");
        $stmt->execute([$restaurantId]);
        return $stmt->fetchAll();
    }
}
<?php
namespace Application;

use PDO;

class Mail
{
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createMail($name, $message, $userId)
    {
        $stmt = $this->db->prepare("INSERT INTO mail (name, message, userid) VALUES (:name, :message, :userid)");
        $stmt->execute([
            'name' => $name,
            'message' => $message,
            'userid' => $userId
        ]);

        return $this->db->lastInsertId();
    }

    public function listMail($userId, $role) 
    {
        if ($role === 'admin') {
            $result = $this->db->query("SELECT id, name, message, userid FROM mail ORDER BY id");
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        $stmt = $this->db->prepare("SELECT id, name, message, userid FROM mail WHERE userid = :userid ORDER BY id");
        $stmt->execute(['userid' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
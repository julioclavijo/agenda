<?php
namespace App\Models;

use PDO;

class AgendaModel {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM agenda");
        if (!$stmt) {
            return false; // La consulta SQL falló
        }
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: []; // Retorna un array vacío si no hay datos
    }

    public function create($fecha, $asunto, $actividad) {
        $stmt = $this->db->prepare("INSERT INTO agenda (fecha, asunto, actividad) VALUES (?, ?, ?)");
        return $stmt->execute([$fecha, $asunto, $actividad]);
    }
}

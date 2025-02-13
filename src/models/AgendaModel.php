<?php
class AgendaModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM agenda";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($fecha, $asunto, $actividad) {
        $query = "INSERT INTO agenda (fecha, asunto, actividad) VALUES (:fecha, :asunto, :actividad)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':asunto', $asunto);
        $stmt->bindParam(':actividad', $actividad);
        return $stmt->execute();
    }
}
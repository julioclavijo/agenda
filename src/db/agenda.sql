CREATE TABLE agenda (
    id_agenda INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    asunto VARCHAR(255) NOT NULL,
    actividad TEXT NOT NULL
);


INSERT INTO agenda (fecha, asunto, actividad) VALUES
('2023-10-01', 'Reunión', 'Planificación del proyecto'),
('2023-10-02', 'Presentación', 'Exposición de resultados');
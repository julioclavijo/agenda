<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use App\Models\AgendaModel;
use App\Database\Database;

function convertToUtf8($array) {
    return array_map(function ($item) {
        return is_string($item) ? mb_convert_encoding($item, 'UTF-8', 'UTF-8') : $item;
    }, $array);
}

return function (App $app) {
    $db = (new Database())->getConnection();
    $agendaModel = new AgendaModel($db);

    $app->get('/actividades', function (Request $request, Response $response) use ($agendaModel) {
        $actividades = $agendaModel->getAll();

        if ($actividades === false) {
            $response->getBody()->write(json_encode(["error" => "Error al obtener actividades"]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }

        // Convertir a UTF-8
        $actividades = array_map('convertToUtf8', $actividades);

        // Convertir a JSON
        $json = json_encode($actividades, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $response->getBody()->write(json_encode(["error" => "Error al convertir datos a JSON"]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write($json);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/actividades', function (Request $request, Response $response) use ($agendaModel) {
        // Decodificar manualmente el JSON
        $body = json_decode($request->getBody()->getContents(), true);

        // Validar si el JSON es válido
        if (!is_array($body)) {
            $response->getBody()->write(json_encode(['error' => 'Formato JSON inválido']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Registrar en logs para depuración
        error_log(print_r($body, true));

        // Validación de datos
        $fecha = $body['fecha'] ?? null;
        $asunto = $body['asunto'] ?? null;
        $actividad = $body['actividad'] ?? null;

        if (empty($fecha) || empty($asunto) || empty($actividad)) {
            $response->getBody()->write(json_encode(['error' => 'Datos incompletos']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Intentar insertar en la base de datos
        try {
            $agendaModel->create($fecha, $asunto, $actividad);
            $response->getBody()->write(json_encode(['success' => true]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            error_log("Error al insertar en la BD: " . $e->getMessage());
            $response->getBody()->write(json_encode(['error' => 'Error interno del servidor']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });
};

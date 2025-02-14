<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use App\Models\AgendaModel;
use App\Database\Database;

return function (App $app) {
    $db = (new Database())->getConnection();
    $agendaModel = new AgendaModel($db);

    function convertToUtf8($array) {
        return array_map(function ($item) {
            return is_string($item) ? mb_convert_encoding($item, 'UTF-8', 'UTF-8') : $item;
        }, $array);
    }
    
    $app->get('/actividades', function (Request $request, Response $response) use ($agendaModel) {
        $actividades = $agendaModel->getAll();
    
        if ($actividades === false) {
            $response->getBody()->write(json_encode(["error" => "Error al obtener actividades"]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    
        // Convertir a UTF-8
        $actividades = array_map('convertToUtf8', $actividades);
    
        $json = json_encode($actividades, JSON_UNESCAPED_UNICODE);
    
        if ($json === false) {
            $response->getBody()->write(json_encode(["error" => "Error al convertir datos a JSON"]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    
        $response->getBody()->write($json);
        return $response->withHeader('Content-Type', 'application/json');
    });
    

    $app->post('/actividades', function (Request $request, Response $response) use ($agendaModel) {
        $data = $request->getParsedBody();
        $result = $agendaModel->create($data['fecha'], $data['asunto'], $data['actividad']);
        $status = $result ? 'Actividad agregada' : 'Error al agregar actividad';
        $response->getBody()->write(json_encode(['status' => $status]));
        return $response->withHeader('Content-Type', 'application/json');
    });

        $app->get('/agenda', function ($request, $response, $args) {
        $response->getBody()->write("Bienvenido a la API de Agenda");
        return $response;
    });
};

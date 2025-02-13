<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use App\Database\Database;


require __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Configurar la aplicación Slim
$app = AppFactory::create();

// Middleware para parsear JSON
$app->addBodyParsingMiddleware();

// Conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Instanciar el modelo
$agendaModel = new AgendaModel($db);

// Endpoint GET para consultar actividades
$app->get('/actividades', function (Request $request, Response $response) use ($agendaModel) {
    $actividades = $agendaModel->getAll();
    $response->getBody()->write(json_encode($actividades));
    return $response->withHeader('Content-Type', 'application/json');
});

// Endpoint POST para agregar una actividad
$app->post('/actividades', function (Request $request, Response $response) use ($agendaModel) {
    $data = $request->getParsedBody();
    $result = $agendaModel->create($data['fecha'], $data['asunto'], $data['actividad']);
    if ($result) {
        $response->getBody()->write(json_encode(['status' => 'Actividad agregada', 'data' => $data]));
    } else {
        $response->getBody()->write(json_encode(['status' => 'Error al agregar actividad']));
    }
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
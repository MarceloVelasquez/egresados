<?php

require '../../vendor/autoload.php';

use Slim\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH");
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = 'localhost';
$config['db']['user']   = 'root';
$config['db']['pass']   = '';
$config['db']['dbname'] = 'egresados';

$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();
$container['upload_directory'] =  'images';
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO(
        'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'],
        $db['pass'],
        [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    return $pdo;
};

require '../routes/facultad.php';
require '../routes/departamento.php';
require '../routes/actividadEconomica.php';
require '../routes/admision.php';
require '../routes/centroEstudios.php';
require '../routes/centroLaboral.php';
require '../routes/colegiatura.php';
require '../routes/contrato.php';
require '../routes/distrito.php';
require '../routes/egresado.php';
require '../routes/escuelaProfesional.php';
require '../routes/estudiosPostgrado.php';
require '../routes/modalidadAdmision.php';
require '../routes/modalidadTitulacion.php';
require '../routes/persona.php';
require '../routes/personal.php';
require '../routes/provincia.php';
require '../routes/titulacion.php';
require '../routes/universidad.php';
require '../routes/usuario.php';
require '../routes/tipoEstudioPostgrado.php';
require '../routes/reportes.php';
require '../routes/estadisticas.php';
require '../routes/recuperar.php';
$app->run();

<?php

use Psr\Http\Message\ServerRequestInterface as Request;


$app->get('/api/carreras', function () {
  try {
    $data = $this->db->query("SELECT codigo,codigoEscuela,codigoPersona,codigoAdmision,fechaInicio,fechaTermino FROM egresado WHERE vigencia=1")->fetchAll();
    if ($data) {
      $result = array('estado' => true, 'data' => $data);
      echo json_encode($result);
    } else {
      echo json_encode(array('estado' => false));
    }
  } catch (PDOException $e) {
    echo '{"Error": { "mensaje": ' . $e->getMessage() . '}';
  }
});

$app->get('/api/carreras/{codigo}', function (Request $request) {
  $codigo = $request->getAttribute('codigo');
  try {
    $data = $this->db->query("SELECT E.codigo,E.codigoEscuela,EP.nombre as nombreEscuela,U.codigo as CodigoUni,U.nombre as universidad,codigoPersona,A.codigoModalidad,codigoAdmision,A.nombre as Admision,A.fechaAdmision as fechaAdmision,fechaInicio,fechaTermino 
                                FROM egresado E 
                                INNER JOIN persona P on P.codigo = codigoPersona 
                                INNER JOIN escuelaProfesional EP on E.codigoEscuela = EP.codigo 
                                INNER JOIN universidad U on EP.codigoUniversidad = U.codigo 
                                INNER JOIN admision A on A.codigo = E.codigoAdmision 
                                WHERE P.dni = $codigo and E.vigencia=1")->fetchAll();
    if ($data) {
      $result = array('estado' => true, 'data' => $data);
      echo json_encode($result);
    } else {
      echo json_encode(array('estado' => false));
    }
  } catch (PDOException $e) {
    echo '{"Error": { "mensaje": ' . $e->getMessage() . '}';
  }
});

$app->post('/api/carreras/add', function (Request $request) {
  $nombreUniversidad = $request->getParam('nombreUniversidad');
  $codigoEscuela = $request->getParam('nombreEscuela');
  $dni = $request->getParam('dni');
  $codigoAdmision = $request->getParam('codigoAdmision');
  $fechaInicio = $request->getParam('fechaInicio');
  $fechaTermino = $request->getParam('fechaTermino');
  // $fechaadmision  nombre codigoModalidad
  try {
    $exist = $this->db->query("SELECT codigo FROM egresado 
  WHERE codigoEscuela = $codigoEscuela and codigoPersona = $codigoPersona")->fetchAll();
    if ($exist) {
      echo json_encode(array('estado' => false, 'mensaje' => 'Ya tiene esa carrera registrada'));
    } else {

      if (!$codigoAdmision) { }

      // $cantidad = $this->db->exec("INSERT INTO egresado(codigoEscuela,codigoPersona,codigoAdmision,fechaInicio,fechaTermino,vigencia) 
      // Values('$codigoEscuela','$codigoPersona',$codigoAdmision,$fechaInicio,$fechaTermino,1)");
      //       if ($cantidad > 0) {
      //       echo json_encode(array('estado' => true));
      //       } else {
      //       echo json_encode(array('estado' => false));
      //       }
    }
  } catch (PDOException $e) {
    echo '{"Error": { "mensaje": ' . $e->getMessage() . '}';
  }
});

$app->put('/api/carreras/{codigo}', function (Request $request) {
  $codigo = $request->getAttribute('codigo');
  $codigoEscuela = $request->getParam('codigoEscuela');
  $codigoPersona = $request->getParam('codigoPersona');
  $codigoAdmision = $request->getParam('codigoAdmision');
  $fechaInicio = $request->getParam('fechaInicio');
  $fechaTermino = $request->getParam('fechaTermino');
  try {
    $cantidad = $this->db->exec("UPDATE egresado set
                                codigoEscuela ='$codigoEscuela',
                                codigoPersona = '$codigoPersona',
                                codigoAdmision = '$codigoAdmision',
                                fechaInicio = '$fechaInicio',
                                fechaTermino = '$fechaTermino'  
                                WHERE codigo = $codigo");
    if ($cantidad > 0) {
      echo json_encode(array('estado' => true));
    } else {
      echo json_encode(array('estado' => false));
    }
  } catch (PDOException $e) {
    echo '{"Error": { "mensaje": ' . $e->getMessage() . '}';
  }
});

$app->delete('/api/carreras/{codigo}', function (Request $request) {
  $codigo = $request->getAttribute('codigo');
  try {
    $cantidad = $this->db->exec("DELETE FROM egresado 
                                WHERE codigo = $codigo");
    if ($cantidad > 0) {
      echo json_encode(array('estado' => true));
    } else {
      echo json_encode(array('estado' => false));
    }
  } catch (PDOException $e) {
    echo '{"Error": { "mensaje": ' . $e->getMessage() . '}';
  }
});

$app->get('/api/carreras/{admision}/{escuelaProfesional}', function (Request $request) {
  $codigoEscuela = $request->getAttribute('escuelaProfesional');
  $codigoAdmision = $request->getAttribute('admision');
  try {
    $data = $this->db->query("SELECT nombres, YEAR(fechaTermino) as Termino, T.codigoEgresado as Titulacion, C.codigoEgresado as Colegiatura
                              FROM egresado INNER JOIN persona on persona.codigo = codigoPersona
                              INNER JOIN titulacion T on egresado.codigo= T.codigoEgresado
                              INNER JOIN colegiatura C on egresado.codigo = C.codigoEgresado
                              WHERE egresado.codigoEscuela = $codigoEscuela and egresado.codigoAdmision = $codigoAdmision")->fetchAll();
    if ($data) {
      $result = array('estado' => true, 'data' => $data);
      echo json_encode($result);
    } else {
      echo json_encode(array('estado' => false));
    }
  } catch (PDOException $e) {
    echo '{"Error": { "mensaje": ' . $e->getMessage() . '}';
  }
});
$app->get('/api/carreras/actividadEconomica/{codigo}', function (Request $request) {
  $codigo = $request->getAttribute('codigo');
  try {
    $data = $this->db->query("SELECT nombres,celular,correo, Centro.razonSocial, C.cargo,C.detalleFunciones, A.nombre
                              FROM egresado INNER JOIN persona on persona.codigo = codigoPersona
                              INNER JOIN contrato C on egresado.codigo= C.codigoEgresado
                              INNER JOIN centroLaboral Centro on C.codigoCentroLaboral = Centro.codigo
                              INNER JOIN actividadEconomica A on Centro.codigoActividad = A.codigo
                              WHERE actividadEconomica.codigo = $codigo")->fetchAll();
    if ($data) {
      $result = array('estado' => true, 'data' => $data);
      echo json_encode($result);
    } else {
      echo json_encode(array('estado' => false));
    }
  } catch (PDOException $e) {
    echo '{"Error": { "mensaje": ' . $e->getMessage() . '}';
  }
});

//Funciones

// function validarCarrera($codigoEscuela,$codigoPersona){
//   $data = $this->db->query("SELECT codigo FROM egresado 
//           WHERE codigoEscuela = $codigoEscuela and codigoPersona = $codigoPersona")->fetchAll();
//       if ($data) {
//         return false;
//       }else {
//         return true;
//       }
// }

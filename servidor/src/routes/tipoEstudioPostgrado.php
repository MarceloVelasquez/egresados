<?php
$app->get('/api/tiposPostgrado', function () {
  $data = $this->db->query("SELECT codigo,nombre,descripcion FROM tipoestudiopostgrado WHERE vigencia=1")->fetchAll();
  if ($data) {
    $result = array('estado' => true, 'data' => $data);
    echo json_encode($result);
  } else {
    echo json_encode(array('estado' => false));
  }
});

<?
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/* ==================================================
    GET
================================================== */

$app->get('/admin', function (Request $request, Response $response) {

  $result = [];
  $sth = $this->db->query('SELECT * FROM teste');

  while($row = $sth->fetch()) {
    array_push($result, $row);
  }

  $response = $this->view->render($response, "admin-home.html", ["message" => NULL]);
  return $response;
})->setName('admin-home');
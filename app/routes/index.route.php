<?
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/* ==================================================
    GET
================================================== */

$app->get('/', function (Request $request, Response $response) {
  $result = [];
  $sth = $this->db->query('SELECT * FROM BackgroundSlider');

  while($row = $sth->fetch()) {
    array_push($result, $row);
  }

  $response = $this->view->render($response, "index.html", ["slides" => $result]);
  return $response;
})->setName('index');
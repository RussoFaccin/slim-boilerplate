<?
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/* ==================================================
    GET
================================================== */

$app->get('/login', function (Request $request, Response $response) {
	$message = $this->flash_message->read();
  $response = $this->view->render($response, "login.html", ["message" => $message]);
  return $response;
})->setName('login');

/* ==================================================
    POST
================================================== */

$app->post('/login', function (Request $request, Response $response) {
  $data = $request->getParsedBody();
  
  $user = $data['fld_user'];
  $pass = $data['fld_pass'];

  $sth = $this->db->prepare("SELECT * FROM users WHERE user_name=:user");
  $sth->execute(['user' => $user]);

  $result = array();
  
  while($row = $sth->fetch()) {
    array_push($result, $row);
  }

  $hash = $result[0]['user_pass'];

  if(!password_verify ( $pass , $hash )){
	  $this->flash_message->add("Verifique os campos Usuário e Senha");
    $path = $this->router->pathFor('login');
    return $response->withStatus(401)->withHeader('Location', "$path");
  }else{
	  $_SESSION['USERNAME'] = $user;
	  $_SESSION['LAST_ACTIVITY'] = time();
  }

  ### JWT
  $raw_header = array(
    'alg' => 'HS256',
    'typ' => 'JWT'
  );

  $raw_payload = array(
    'user_name' => $result[0]['user_name'],
    'user_mail' => $result[0]['user_mail']
  );

  $jwt_header = base64_encode(json_encode($raw_header));
  $jwt_payload = base64_encode(json_encode($raw_payload));
  $jwt_signature = hash_hmac("sha256", $jwt_header.".".$jwt_payload, getenv("SECRET"));
  $jwt_token = $jwt_header.".".$jwt_payload.".".$jwt_signature;

  $_SESSION['jwt'] = $jwt_token;
  $_SESSION['IS_LOGGED'] = true;
  $_SESSION['ACTIVE'] = time();

  $path = $this->router->pathFor('admin-home');
  return $response->withStatus(200)->withHeader('Location', "$path");
});
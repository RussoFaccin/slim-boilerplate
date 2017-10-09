<?
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/* ==================================================
    GET
================================================== */

$app->get('/', function (Request $request, Response $response) {

    // SLUGIFY
    print_r(slugify("Hello World"));
  
  	// PASSWORD - GENERATOR
    $raw_pass = "admin";
    $pass = password_hash($raw_pass, PASSWORD_DEFAULT);
  
  	// JWT - GENERATOR
    $raw_header = array(
    'alg' => 'HS256',
    'typ' => 'JWT'
    );

    $raw_payload = array(
    'user_name' => 'admin',
    'user_mail' => 'ppm@ppm.com.br'
    );

    $jwt_header = base64_encode(json_encode($raw_header));
    $jwt_payload = base64_encode(json_encode($raw_payload));
    $jwt_signature = hash_hmac("sha256", $jwt_header.".".$jwt_payload, getenv("SECRET"));
    $jwt_token = $jwt_header.".".$jwt_payload.".".$jwt_signature;

    $result = [];
    $sth = $this->db->query('SELECT * FROM teste');

    while ($row = $sth->fetch()) {
        array_push($result, $row);
    }

    $response = $this->view->render($response, "index.html", ["message" => "Index Page", "slides" => $result]);
    return $response;
})->setName('index');

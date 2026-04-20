<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CertificazioniController
{
  public function index(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM certificazioni");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }

  public function display(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM certificazioni");
    $results = $result->fetch_all(MYSQLI_ASSOC);
    if(count($results) > 0){
      $response->getBody()->write(json_encode($results));
      return $response->withHeader("Content-type", "application/json")->withStatus(200);
    } else {
      return $response->withHeader("Content-type", "application/json")->withStatus(404);
    }
  }
  
  public function show(Request $request, Response $response, $args){
    $id = $args['id'];
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM certificazioni WHERE id = $id");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    if(count($results) > 0){
      $response->getBody()->write(json_encode($results[0]));
      return $response->withHeader("Content-type", "application/json")->withStatus(200);
    } else {
      return $response->withHeader("Content-type", "application/json")->withStatus(404);
    }
  }

  // helper: supporta body JSON/raw, getParsedBody() e fallback a $_POST
  private function getRequestData(Request $request){
    $data = $request->getParsedBody();
    if (is_array($data) && count($data) > 0) {
      return $data;
    }
    $body = (string)$request->getBody();
    if ($body !== '') {
      $json = json_decode($body, true);
      if (is_array($json)) return $json;
    }
    if (!empty($_POST)) return $_POST;
    return [];
  }

  public function create(Request $request, Response $response, $args){
    $data = $this->getRequestData($request);
    $certificazione_id = isset($data['certificazione_id']) ? trim($data['certificazione_id']) : '';
    $titolo = isset($data['titolo']) ? trim($data['titolo']) : '';
    $votazione = isset($data['votazione']) ? trim($data['votazione']) : '';
    $ente = isset($data['ente']) ? trim($data['ente']) : '';

    if ($certificazione_id === '' || $titolo === '' || $votazione === '' || $ente === '') {
      $response->getBody()->write(json_encode(['error' => 'Missing data']));
      return $response->withHeader("Content-type", "application/json")->withStatus(400);
    }

    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $certificazione_id = $mysqli_connection->real_escape_string($certificazione_id);
    $titolo = $mysqli_connection->real_escape_string($titolo);
    $votazione = $mysqli_connection->real_escape_string($votazione);
    $ente = $mysqli_connection->real_escape_string($ente);

    $result = $mysqli_connection->query("INSERT INTO certificazioni (certificazione_id, titolo, votazione, ente) VALUES ('$certificazione_id', '$titolo','$votazione','$ente')");
    
    if($result){
      return $response->withHeader("Content-type", "application/json")->withStatus(201);
    } else {
      return $response->withHeader("Content-type", "application/json")->withStatus(500);
    }
  }

  public function update(Request $request, Response $response, $args){
    $id = isset($args['id']) ? intval($args['id']) : 0;
    $data = $this->getRequestData($request);
    $certificazione_id = isset($data['certificazione_id']) ? trim($data['certificazione_id']) : '';
    $titolo = isset($data['titolo']) ? trim($data['titolo']) : '';
    $votazione = isset($data['votazione']) ? trim($data['votazione']) : '';
    $ente = isset($data['ente']) ? trim($data['ente']) : '';

    if ($id <= 0 || $certificazione_id === '' || $titolo === '' || $votazione === '' || $ente === '') {
      $response->getBody()->write(json_encode(['error' => 'Invalid id or missing fields']));
      return $response->withHeader("Content-type", "application/json")->withStatus(400);
    }

    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $certificazione_id = $mysqli_connection->real_escape_string($certificazione_id);
    $titolo = $mysqli_connection->real_escape_string($titolo);
    $votazione = $mysqli_connection->real_escape_string($votazione);
    $ente = $mysqli_connection->real_escape_string($ente);

    $result = $mysqli_connection->query("UPDATE certificazioni SET certificazione_id='$certificazione_id', titolo='$titolo', votazione='$votazione', ente='$ente' WHERE id=$id");
    
    if($result){
      $response->getBody()->write(json_encode(['message' => 'Updated']));
      return $response->withHeader("Content-type", "application/json")->withStatus(200);
    } else {
      $response->getBody()->write(json_encode(['error' => $mysqli_connection->error]));
      return $response->withHeader("Content-type", "application/json")->withStatus(500);
    }
  }

  public function destroy(Request $request, Response $response, $args){
    $id = isset($args['id']) ? intval($args['id']) : 0;
    if ($id <= 0) {
      $response->getBody()->write(json_encode(['error' => 'Invalid id']));
      return $response->withHeader("Content-type", "application/json")->withStatus(400);
    }

    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("DELETE FROM certificazioni WHERE id=$id");
    
    if($result){
      $response->getBody()->write(json_encode(['message' => 'Deleted']));
      return $response->withHeader("Content-type", "application/json")->withStatus(200);
    } else {
      $response->getBody()->write(json_encode(['error' => $mysqli_connection->error]));
      return $response->withHeader("Content-type", "application/json")->withStatus(500);
    }
  }
}

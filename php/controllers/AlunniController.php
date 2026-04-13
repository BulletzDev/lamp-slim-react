<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AlunniController
{
  public function index(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM alunni");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }

  public function display(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM alunni");
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
    $result = $mysqli_connection->query("SELECT * FROM alunni WHERE id = $id");
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
    $nome = isset($data['nome']) ? trim($data['nome']) : '';
    $cognome = isset($data['cognome']) ? trim($data['cognome']) : '';

    if ($nome === '' || $cognome === '') {
      $response->getBody()->write(json_encode(['error' => 'Missing nome or cognome']));
      return $response->withHeader("Content-type", "application/json")->withStatus(400);
    }

    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $nome = $mysqli_connection->real_escape_string($nome);
    $cognome = $mysqli_connection->real_escape_string($cognome);

    $result = $mysqli_connection->query("INSERT INTO alunni (nome, cognome) VALUES ('$nome', '$cognome')");
    
    if($result){
      return $response->withHeader("Content-type", "application/json")->withStatus(201);
    } else {
      return $response->withHeader("Content-type", "application/json")->withStatus(500);
    }
  }

  public function update(Request $request, Response $response, $args){
    $id = isset($args['id']) ? intval($args['id']) : 0;
    $data = $this->getRequestData($request);
    $nome = isset($data['nome']) ? trim($data['nome']) : '';
    $cognome = isset($data['cognome']) ? trim($data['cognome']) : '';

    if ($id <= 0 || $nome === '' || $cognome === '') {
      $response->getBody()->write(json_encode(['error' => 'Invalid id or missing nome/cognome']));
      return $response->withHeader("Content-type", "application/json")->withStatus(400);
    }

    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $nome = $mysqli_connection->real_escape_string($nome);
    $cognome = $mysqli_connection->real_escape_string($cognome);

    $result = $mysqli_connection->query("UPDATE alunni SET nome='$nome', cognome='$cognome' WHERE id=$id");
    
    if($result){
      return $response->withHeader("Content-type", "application/json")->withStatus(200);
    } else {
      return $response->withHeader("Content-type", "application/json")->withStatus(500);
    }
  }

  public function destroy(Request $request, Response $response, $args){
    $id = $args['id'];
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("DELETE FROM alunni WHERE id=$id");
    
    if($result){
      return $response->withHeader("Content-type", "application/json")->withStatus(200);
    } else {
      return $response->withHeader("Content-type", "application/json")->withStatus(500);
    }
  }
}

<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../config/db.php';

$app = AppFactory::create();
$app->get('/student',function(Request $request, Response $response){
    try{
        $db = new DB();
        $conn= $db.connect();

        $query = $conn->query('SELECT * FROM student');
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        $response->getBody()->write(json_encode($results));
        return $response->withHeader("Content-Type", "application/json")
        ->withStatus(200);
    }
    catch( PDOException $e){
        
    }
    
   });



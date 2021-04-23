<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();


$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->setBasePath('/app');

$app->get('/', function (Request $request, Response $response, $args) {
$response->getBody()->write("Hello WOrld");
return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->get('/users',function(Request $request, Response $response){
    $dbUser="root";       
    $dbPassword="";     
    try{  
        $dbConn= new PDO("mysql:host=localhost;dbname=test",$dbUser,$dbPassword);  
       $qp = $request->getQueryParams();
       //pagination
        if ($qp != null){
            if ($qp['page'] != null){
                $pg= $qp['page'];
                $offset = $pg*2-1;
                $query = $dbConn->query("SELECT * FROM users LIMIT 2 OFFSET $offset ");
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                $response->getBody()->write(json_encode($results));
                return $response->withHeader("Content-Type", "application/json")
                ->withStatus(200);
            }
            else if($qp['search'] != null){
                $name= $qp['search'];
                $query = $dbConn->query("SELECT * FROM users WHERE name= '$name'");
                $results = $query->fetch(PDO::FETCH_OBJ);
                $response->getBody()->write(json_encode($results));
                return $response->withHeader("Content-Type", "application/json")
                ->withStatus(200); 
            }
           
    
        }
        else{
            $query = $dbConn->query('SELECT * FROM users');
            $results = $query->fetchAll(PDO::FETCH_OBJ);
            $response->getBody()->write(json_encode($results));
            return $response->withHeader("Content-Type", "application/json")
            ->withStatus(200);
        }  
    } 
    catch(Exception $e){  
         $e->getMessage();  
    }  
   
    
});

$app->get('/users/{id}',function(Request $request, Response $response, $args){
    $dbUser="root";       
    $dbPassword="";       
    try{  
        $dbConn= new PDO("mysql:host=localhost;dbname=test",$dbUser,$dbPassword);  
        $id= $args['id'];
        $query = $dbConn->query("SELECT * FROM users WHERE id= $id");
            $results = $query->fetch(PDO::FETCH_OBJ);
            if ($results != null){
                $response->getBody()->write(json_encode($results));
            return $response->withHeader("Content-Type", "application/json")
            ->withStatus(200); 
            }
            else{
                return $response->withHeader("Content-Type", "application/json")
            ->withStatus(404);
            }
            
    } 
    catch(Exception $e){  
        $e->getMessage();  
    }  
   
});

$app->put('/users/{id}', function (Request $request, Response $response, $args) {
     $qp = $request->getQueryParams();
    $dbUser="root";        
    $dbPassword="";      
    try{  
        $dbConn= new PDO("mysql:host=localhost;dbname=test",$dbUser,$dbPassword);  
        $id=$args['id']; 
        $name=$qp["name"];
        $email= $qp['email'];
        $password=$qp['password'];
        if ($name==null) {
            throw new InvalidArgumentException('Name should be provided');
        }
        if ($email==null) {
            throw new InvalidArgumentException('Email should provided');
        }
        if ($password==null) {
            throw new InvalidArgumentException('Passwords Should be provided');
        }
        $result=$dbConn->query("SELECT Count(*) FROM `users` WHERE id=$id")->fetch();
        $count=$result[0];
        if ($count==1){
            $sql="UPDATE users SET name= '$name',password='$password',email='$email' WHERE id=$id";
        $dbConn->query($sql);
        $response->getBody()->write(json_encode($qp));
        return $response->withHeader("Content-Type", "application/json")
        ->withStatus(200);
        }
        else{
            $sql="INSERT INTO `users` (`id`,`name`, `email`, `password`) VALUES ('$id','$name', '$email', '$password')";  
            $dbConn->query($sql);
            $response->getBody()->write(json_encode($qp));
            return $response->withHeader("Content-Type", "application/json")->withStatus(201);
        }
    } 
    catch(Exception $e){  
        echo $e->getMessage();  
    } 
        
});

$app->delete('/users/{id}', function (Request $request, Response $response, $args) {
    $dbUser="root";       
    $dbPassword="";      
    try{  
        $dbConn= new PDO("mysql:host=localhost;dbname=test",$dbUser,$dbPassword);  
        $id=$args['id'];
        $sql="DELETE FROM users
        WHERE id=$id";
        $dbConn->query($sql);
        return $response->withHeader("Content-Type", "application/json")
        ->withStatus(204);
    } 
    catch(Exception $e){  
    Echo "Connection failed" . $e->getMessage();  
    } 
    
});

$app->post('/users',function(Request $request, Response $response){
    $gb = json_decode($request->getBody());
    $name = ($gb->name);
    $email = ($gb->email);
    $password = ($gb->password);
    if ($name==null) {
        throw new InvalidArgumentException('Name should be provided');
    }
    if ($email==null) {
        throw new InvalidArgumentException('Email should provided');
    }
    if ($password==null) {
        throw new InvalidArgumentException('Passwords Should be provided');
    }
    $sql="INSERT INTO `users` (`name`, `email`, `password`) VALUES ('$name', '$email', '$password')";
    //db conn
    $dbUser="root";      
    $dbPassword="";      
    try{  
        $dbConn= new PDO("mysql:host=localhost;dbname=test",$dbUser,$dbPassword);  
        $dbConn->query($sql);
    return $response->withHeader("Content-Type", "application/json")->withStatus(201);
 
    } 
    catch(Exception $e){  
    Echo "Connection failed" . $e->getMessage();  
    } 
    
});

$app->post('/login',function(Request $request, Response $response){
    $gb = json_decode($request->getBody());
    $email = ($gb->email);
    $password = ($gb->password);
    if ($email==null) {
        throw new InvalidArgumentException('Email should provided');
    }
    if ($password==null) {
        throw new InvalidArgumentException('Passwords Should be provided');
    }
    $sql="SELECT * from users WHERE `email`='$email' and `password`='$password'";
    //db conn
    $dbUser="root";      
    $dbPassword="";     
    try{  
        $dbConn= new PDO("mysql:host=localhost;dbname=test",$dbUser,$dbPassword);  
    } 
    catch(Exception $e){  
    Echo "Connection failed" . $e->getMessage();  
    } 
    $count=0;
    foreach ($dbConn->query($sql) as $row) {
        $count++;
    }
    if($count == 1 && !empty($row)) {
      $msg="Login succesfull"; 
      $status=200;
    } else {
      $msg = "Invalid username and password!";
      $status=404;
    }
    $response->getBody()->write(json_encode($msg));
    return $response->withHeader("Content-Type", "application/json")->withStatus($status);
});

$app->run();

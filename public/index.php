<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
// require __DIR__ . '/../config/db.php';


$app = AppFactory::create();
// require __DIR__ . '/../routes/user.php';

$app->addRoutingMiddleware();
// 
// $app->addErrorMiddleware(true, true, true);

$app->setBasePath('/app');

$app->get('/', function (Request $request, Response $response, $args) {
$response->getBody()->write("Hello WOLLLL");
return $response;
});

$app->get('/recent', function (Request $request, Response $response) {
    $results = [
        'id' => '1',
        'title' => 'Post Title',
        'body' => 'Post body'
    ];
    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-Type", "application/json");
});

$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->get('/student',function(Request $request, Response $response){
    $dbUser="root";      //by default root is user name.  
    $dbPassword="";     //password is blank by default  
    try{  
        $dbConn= new PDO("mysql:host=localhost;dbname=test",$dbUser,$dbPassword);  
        Echo "Successfully connected with myDB database";  
    } 
    catch(Exception $e){  
    Echo "Connection failed" . $e->getMessage();  
    }  
    $query = $dbConn->query('SELECT * FROM student');
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        $response->getBody()->write(json_encode($results));
        return $response->withHeader("Content-Type", "application/json")
        ->withStatus(200);
});

$app->get('/student/{id}',function(Request $request, Response $response, $args){
    $dbUser="root";      //by default root is user name.  
    $dbPassword="";     //password is blank by default  
    try{  
        $dbConn= new PDO("mysql:host=localhost;dbname=test",$dbUser,$dbPassword);  
        Echo "Successfully connected with myDB database";  
    } 
    catch(Exception $e){  
    Echo "Connection failed" . $e->getMessage();  
    }  
    $id= $args['id'];
    $query = $dbConn->query("SELECT * FROM student WHERE id= $id");
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        $response->getBody()->write(json_encode($results));
        return $response->withHeader("Content-Type", "application/json")
        ->withStatus(200);
});
//ERRORS
$app->put('/student/update/{id}', function (Request $request, Response $response, $args) {
    $parsedBody = $request->getQueryParams();
    $message = 'Name is:' . $parsedBody["name"] .' ';
    $message .= "Percentage is:". $parsedBody['percentage'] . "";
    $message .= "Rating is:". $parsedBody['rating'] . "";
    $message .= "The roll id is: " . $args['id'];
    $response->getBody()->write($message);
    $dbUser="root";      //by default root is user name.  
    $dbPassword="";     //password is blank by default  
    try{  
        $dbConn= new PDO("mysql:host=localhost;dbname=test",$dbUser,$dbPassword);  
        Echo "Successfully connected with myDB database";  
    } 
    catch(Exception $e){  
    Echo "Connection failed" . $e->getMessage();  
    } 
    $id=$args['id']; 
    $name=$parsedBody["name"];
    $percentage= $parsedBody['percentage'];
    $rating=$parsedBody['rating'];
    $sql="UPDATE student SET name='$name',percentage=$percentage,rating=$rating WHERE id=$id";
    $dbConn->query($sql);
    $response->getBody()->write(json_encode(['message'=>'student updated  '. $id. $name. $percentage. $rating]));
    return $response;
});

$app->delete('/student/del/{id}', function (Request $request, Response $response, $args) {
    $message = "The object id to delete is: " . $args['id'];
    $response->getBody()->write($message);
    //db conn
    $dbUser="root";      //by default root is user name.  
    $dbPassword="";     //password is blank by default  
    try{  
        $dbConn= new PDO("mysql:host=localhost;dbname=test",$dbUser,$dbPassword);  
        Echo "Successfully connected with database";  
    } 
    catch(Exception $e){  
    Echo "Connection failed" . $e->getMessage();  
    } 
    //sql run
    $id=$args['id'];
    $sql="DELETE FROM Student
    WHERE id=$id";
    $dbConn->query($sql);
    $response->getBody()->write(json_encode(['message'=>'student deleted with id  '. $id]));
    return $response;
});

$app->post('/student',function(Request $request, Response $response){
    $parsedBody = $request->getQueryParams();
    $id = $parsedBody["id"];
    $name = $parsedBody["name"];
    $percentage = $parsedBody["percentage"];
    $rating = $parsedBody["rating"];
    $sql="INSERT INTO `student` (`id`, `name`, `percentage`, `rating`) VALUES ($id, '$name', $percentage, $rating)";
    //db conn
    $dbUser="root";      //by default root is user name.  
    $dbPassword="";     //password is blank by default  
    try{  
        $dbConn= new PDO("mysql:host=localhost;dbname=test",$dbUser,$dbPassword);  
        Echo "Successfully connected with database";  
    } 
    catch(Exception $e){  
    Echo "Connection failed" . $e->getMessage();  
    } 
    $dbConn->query($sql);
    
    $response->getBody()->write(json_encode(['message'=>'student saved  '. $id. $name. $percentage. $rating]));
    return $response->withHeader("Content-Type", "application/json");
});

$app->post('/login',function(Request $request, Response $response){
    $parsedBody = $request->getQueryParams();
    $email = $parsedBody["email"];
    $password = $parsedBody["password"];
    $sql="SELECT * from Login WHERE `email`='$email' and `password`='$password'";
    //db conn
    $dbUser="root";      //by default root is user name.  
    $dbPassword="";     //password is blank by default  
    try{  
        $dbConn= new PDO("mysql:host=localhost;dbname=test",$dbUser,$dbPassword);  
        Echo "Successfully connected with database";  
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
    } else {
      $msg = "Invalid username and password!";
    }
    $response->getBody()->write(json_encode(['message'=>$msg]));
    return $response->withHeader("Content-Type", "application/json");
});

$app->run();

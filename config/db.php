<?php  
    $dbUser="root";      //by default root is user name.  
    $dbPassword="";     //password is blank by default  
    try{  
        $dbConn= new PDO("mysql:host=localhost;dbname=test",$dbUser,$dbPassword);  
        Echo "Successfully connected with myDB database";  
    } catch(Exception $e){  
    Echo "Connection failed" . $e->getMessage();  
    }  
?>  
<?php
$host="localhost";
    $db="symbi416_hedera";
    $user="symbi416_admin";
    $pwd="Symb10sis2022";

    try{
        $dbconnection=new PDO("mysql:host=$host;dbname=$db",$user, $pwd);
        if($dbconnection){
            //echo "<br/>Connected to database...";
        }
    }catch (Exception $ex) {
        echo "<br/>".$ex->getMessage();
    }

?>
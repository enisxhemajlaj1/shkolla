<?php
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "bc10_blog";
    $dsn = "mysql:host=$host;port=3306;dbname=$database;charset=utf8";

    $pdo = new PDO($dsn, $username, $password);
?>
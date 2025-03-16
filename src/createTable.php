<?php

$mysql = new mysqli(hostname: "8092a0bbc26b", username: "root",password: "root",port: 3306);

$sql = 'CREATE DATABASE test;';
$mysql->query($sql);

$mysql->select_db('test');

$sql = 'CREATE TABLE users (id INT, name VARCHAR(52), surname VARCHAR(52), email VARCHAR(52));';
$mysql->query($sql);

$id = 1;
$name = 'user';
$surname = 'user';
$email = 'user@gmail.com';

$sql = 'INSERT users(id, name, surname, email) VALUES(?, ?, ?, ?)';
$stmt = $mysql->prepare($sql);
$stmt->bind_param('isss',$id, $name, $surname, $email);
$stmt->execute();


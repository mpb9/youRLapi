<?php
/*
  DB NAME: id19496844_mshare
	DB USER: id19496844_michaelbeebe9
	DB HOST: localhost
	DB PASSWORD: Huge/Caniac9!
  $pdo = new PDO('mysql:host=localhost;dbname=mshare', 'mpb9', '9Chester!');
        server= c:/wamp64/www
  $pdo = new PDO('mysql:host=localhost;dbname=id19496844_mshare', 'id19496844_michaelbeebe9', 'Huge/Caniac9!');
        server= /storage/ssd3/844/19496844/public_html
*/
try {
  $pdo = new PDO('mysql:host=localhost;dbname=id19496844_mshare', 'id19496844_michaelbeebe9', 'Huge/Caniac9!');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->exec('SET NAMES "utf8"');

} catch (PDOException $e) {

  $error = 'Unable to connect to the database server.';
  echo $error;
  
  exit();
}


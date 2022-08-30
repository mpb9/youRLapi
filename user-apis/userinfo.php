<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/db.inc.php';
include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/helpers.inc.php';

$restJson = file_get_contents("php://input");
$_POST = json_decode($restJson, true);

if (empty($_POST['name'])) die();

$name = $_POST['name'];

try
{
  $sql = "SELECT * FROM user
    WHERE name = :name";
  $s = $pdo->prepare($sql);
  $s->bindValue(':name', $name);
  $s->execute();
}
catch (PDOException $e)
{
  $error = 'Error finding user info';
  include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
  exit();
}

while(($row = $s->fetch(PDO::FETCH_ASSOC)) != false){
  $info[] = array(
    'email' => $row['email'],
    'fullname' => $row['fullname'],
    'bio' => $row['bio'],
    'img' => $row['img']
  );
}

echo json_encode($info[0]);

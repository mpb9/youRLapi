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
  $sql = "SELECT lastpost FROM user
    WHERE name = :name";
  $s = $pdo->prepare($sql);
  $s->bindValue(':name', $name);
  $s->execute();
}
catch (PDOException $e)
{
  $error = 'Error finding last post';
  include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
  exit();
}

$row = $s->fetch(PDO::FETCH_ASSOC);

date_default_timezone_set("America/New_York");
$lastpost = $row['lastpost'];


if(!is_null($lastpost)) {
  echo json_encode($lastpost);
} else {
  echo json_encode(true);
}

<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/db.inc.php';
include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/helpers.inc.php';


$restJson = file_get_contents("php://input");
$_POST = json_decode($restJson, true);

if (empty($_POST['name']) || empty($_POST['imgpath'])) die();

$name = $_POST['name'];
$imgpath = $_POST['imgpath'];

try {
  $sql = 'UPDATE user
      SET img = :imgpath
      WHERE name = :name';
  $s = $pdo->prepare($sql);
  $s->bindValue(':name', $name);
  $s->bindValue(':imgpath', $imgpath);
  $s->execute();
}
catch (PDOException $e)
{
  $error = 'Error adding user img.';
  include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
  exit();
}

echo json_encode('done');
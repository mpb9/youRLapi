<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/db.inc.php';
include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/helpers.inc.php';

$restJson = file_get_contents("php://input");
$_POST = json_decode($restJson, true);

if (empty($_POST['name']) || empty($_POST['password'])) die();

try
{
  $password = md5($_POST['password'] . 'ms');
  $name = $_POST['name'];

  $sql = 'SELECT COUNT(*) FROM user
      WHERE name = :name AND password = :password';
  $s = $pdo->prepare($sql);
  $s->bindValue(':name', $name);
  $s->bindValue(':password', $password);
  $s->execute();
}
catch (PDOException $e)
{
  $error = 'Error searching for user.';
  include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
  exit();
}

$row = $s->fetch();

if ($row[0] > 0)
{
   echo json_encode($_POST['name']);
}
else
{
    echo ('User Not Found');
}
<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include $_SERVER['DOCUMENT_ROOT'] . '/youRLapi/includes/db.inc.php';
include $_SERVER['DOCUMENT_ROOT'] . '/youRLapi/includes/helpers.inc.php';

$restJson = file_get_contents("php://input");
$_POST = json_decode($restJson, true);

if (!empty($_POST['commenter'])){
  $name = $_POST['commenter'];
  $user = $_POST['user'];
} else {
  if (empty($_POST['name'])) die();

  $name = $_POST['name'];
  $user = $_POST['user'];
}

try
{
  $sql = "SELECT COUNT(*) FROM follows
    WHERE following = :name
    AND follower = :user";
  $s = $pdo->prepare($sql);
  $s->bindValue(':name', $name);
  $s->bindValue(':user', $user);
  $s->execute();
}
catch (PDOException $e)
{
  $error = 'Error finding following info';
  include $_SERVER['DOCUMENT_ROOT'] . '/youRLapi/includes/error.html.php';
  exit();
}

$row = $s->fetch();

if($row[0] >= 1) echo json_encode('unfollow');
else {
  echo json_encode('follow');
}

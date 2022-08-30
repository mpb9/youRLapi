<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/db.inc.php';
include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/helpers.inc.php';

$restJson = file_get_contents("php://input");
$_POST = json_decode($restJson, true);

if (empty($_POST['name'])) die();

$name = $_POST['name'];
$user = $_POST['user'];
$relationship = $_POST['follows'];


if($relationship === 'unfollow'){ 
  try {
    $sql = "DELETE FROM follows
      WHERE following = :name
      AND follower = :user";
    $s = $pdo->prepare($sql);
    $s->bindValue(':name', $name);
    $s->bindValue(':user', $user);
    $s->execute();
  } catch (PDOException $e) {
    $error = 'Error unfollowing user';
    include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
    exit();
  }
  echo json_encode('follow');

} else { 
  try {
    $sql = 'INSERT INTO follows SET
    following = :name,
    follower = :user';
    $s = $pdo->prepare($sql);
    $s->bindValue(':name', $name);
    $s->bindValue(':user', $user);
    $s->execute();
  } catch (PDOException $e) {
    $error = 'Error following user';
    include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
    exit();
  }

  echo json_encode('unfollow');
}

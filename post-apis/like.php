<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/db.inc.php';
include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/helpers.inc.php';

$restJson = file_get_contents("php://input");
$_POST = json_decode($restJson, true);

if (empty($_POST['name'])) die();
$id = $_POST['postid'];
$viewer = $_POST['user'];

try
{
  $sql = "SELECT COUNT(*) FROM likes
    WHERE postid = :id
    AND liker = :viewer";
  $s = $pdo->prepare($sql);
  $s->bindValue(':id', $id);
  $s->bindValue(':viewer', $viewer);
  $s->execute();
}
catch (PDOException $e)
{
  $error = 'Error finding likes';
  include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
  exit();
}


$row = $s->fetch();

if($row[0] < 1){
  try {
    $sql = 'UPDATE media 
    SET likes = likes + 1
    WHERE mediaid = :id';
    $s = $pdo->prepare($sql);
    $s->bindValue(':id', $id);
    $s->execute();
  } catch (PDOException $e) {
    $error = 'Error liking.';
    include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
    exit();
  }

  try {
    $sql = 'INSERT INTO likes SET
    postid = :id,
    liker = :viewer';
    $s = $pdo->prepare($sql);
    $s->bindValue(':id', $id);
    $s->bindValue(':viewer', $viewer);
    $s->execute();
  } catch (PDOException $e) {
    $error = 'Error adding to like table.';
    include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
    exit();
  }

  echo json_encode(true);

} else {
  try {
    $sql = 'UPDATE media 
    SET likes = likes - 1
    WHERE mediaid = :id';
    $s = $pdo->prepare($sql);
    $s->bindValue(':id', $id);
    $s->execute();
  } catch (PDOException $e) {
    $error = 'Error unliking.';
    include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
    exit();
  }

  try {
    $sql = 'DELETE FROM likes
    WHERE postid = :id
    AND liker = :viewer';
    $s = $pdo->prepare($sql);
    $s->bindValue(':id', $id);
    $s->bindValue(':viewer', $viewer);
    $s->execute();
  } catch (PDOException $e) {
    $error = 'Error removing like from table.';
    include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
    exit();
  }

  echo json_encode(false);
}

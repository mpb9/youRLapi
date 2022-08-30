<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/db.inc.php';
include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/helpers.inc.php';


$restJson = file_get_contents("php://input");
$_POST = json_decode($restJson, true);

if (empty($_POST['name'])) die();

$poster = $_POST['name'];
$postid = $_POST['postid'];
$commenter = $_POST['user'];
$comment = $_POST['newComment'];

try
{
  $sql = "INSERT INTO comments SET
    postid = :postid,
    commenter = :commenter,
    comment = :comment";
  $s = $pdo->prepare($sql);
  $s->bindValue(':postid', $postid);
  $s->bindValue(':commenter', $commenter);
  $s->bindValue(':comment', $comment);
  $s->execute();
}
catch (PDOException $e)
{
  $error = 'Error adding to comment db';
  include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
  exit();
}

try {
  $sql = 'UPDATE media 
  SET comments = comments + 1
  WHERE mediaid = :postid';
  $s = $pdo->prepare($sql);
  $s->bindValue(':postid', $postid);
  $s->execute();
} catch (PDOException $e) {
  $error = 'Error commenting.';
  include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
  exit();
}


echo json_encode($comment);

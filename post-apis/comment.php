<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/db.inc.php';
include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/helpers.inc.php';


$restJson = file_get_contents("php://input");
$_POST = json_decode($restJson, true);

if (empty($_POST['name'])) die();

$name = $_POST['name'];
$id = $_POST['postid'];

try
{
  $sql = "SELECT * FROM comments
    JOIN media
    ON comments.postid = media.mediaid
    WHERE comments.postid = :id
    AND media.mediaid = :id
    ORDER BY comments.commentid ASC";
  $s = $pdo->prepare($sql);
  $s->bindValue(':id', $id);
  $s->execute();
}
catch (PDOException $e)
{
  $error = 'Error finding comments';
  include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
  exit();
}

$num = 0;
while(($row = $s->fetch(PDO::FETCH_ASSOC)) != false){
  $comments[] = array(
    'id' => $row['commentid'],
    'postid' => $row['postid'],
    'commenter' => $row['commenter'], 
    'comment' => $row['comment']
  );
  $num=1;
}

if($num != 0) echo json_encode($comments);
else {
  $comments[] = array(
    'id' => 0,
    'postid' => 0,
    'commenter' => 'No comments yet!', 
    'comment' => ''
  );
  echo json_encode($comments);
}
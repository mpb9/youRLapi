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
  $sql = "SELECT * FROM media
    WHERE username = :username
    ORDER BY mediaid DESC";
  $s = $pdo->prepare($sql);
  $s->bindValue(':username', $name);
  $s->execute();
}
catch (PDOException $e)
{
  $error = 'Error finding your posts';
  include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
  exit();
}
$num = 0;

while(($row = $s->fetch(PDO::FETCH_ASSOC)) != false){
  $posts[] = array(
    'id' => $row['mediaid'],
    'poster' => $row['username'], 
    'title' => $row['title'],
    'url' => $row['url'],
    'img' => $row['mediaimg'],
    'caption' => $row['caption'],
    'date' => $row['date']
  );
  $num =1;
}


if($num !== 0) echo json_encode($posts);
else {
  $posts[] = array(
    'id' => 0,
    'poster' => 'no_youRLs_found', 
    'title' => 'Go post a youRL!',
    'url' => 'https://www.webfx.com/blog/social-media/social-media-101/',
    'img' => 'https://ourculturemag.com/wp-content/uploads/2020/10/Spirited_Away_040.jpg',
    'caption' => 'No need to worry, just show people something you like!',
    'date' => date('Y-m-d')
  );
  echo json_encode($posts);
}
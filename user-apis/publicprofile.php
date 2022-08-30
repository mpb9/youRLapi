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
    JOIN media
    ON media.userid = user.id
    WHERE media.username = :name
    ORDER BY media.mediaid DESC
    LIMIT 1";
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

$num = 0;

while(($row = $s->fetch(PDO::FETCH_ASSOC)) != false){
  $info[] = array(
    'fullname' => $row['fullname'],
    'date' => $row['date'],
    'title' => $row['title'],
    'url' => $row['url'],
    'img' => $row['mediaimg'],
    'caption' => $row['caption'],
    'bio' => $row['bio'],
    'proimg' => $row['img']
  );
  $num = 1;
}

if($num !== 0) echo json_encode($info[0]);
else {

  try{
    $sql = "SELECT * FROM user
      WHERE name = :name";
    $s = $pdo->prepare($sql);
    $s->bindValue(':name', $name);
    $s->execute();
  } catch (PDOException $e) {
    $error = 'Error finding user info';
    include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
    exit();
  }

  while(($row = $s->fetch(PDO::FETCH_ASSOC)) != false){
    $info[] = array(
      'fullname' => $row['fullname'],
      'date' => date('Y-m-d'),
      'title' => 'No youRLs yet..',
      'url' => 'https://thelife.com/19-ways-to-encourage-others',
      'img' => 'https://image.freepik.com/free-vector/funny-detective-character-illustration_152098-230.jpg',
      'caption' => 'This user has yet to share anything!',
      'bio' => '',
      'proimg' => ''
    );
  }

  echo json_encode($info[0]);

}

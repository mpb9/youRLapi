<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/db.inc.php';
include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/helpers.inc.php';


$restJson = file_get_contents("php://input");
$_POST = json_decode($restJson, true);

if (empty($_POST['name']) || empty($_POST['title']) || empty($_POST['url'])) die();

$username = $_POST['name'];

try
{
  $sql = 'SELECT id FROM user
      WHERE name = :name';
  $s = $pdo->prepare($sql);
  $s->bindValue(':name', $username);
  $s->execute();
}
catch (PDOException $e)
{
  $error = 'Error finding user';
  include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
  exit();
}

$row = $s->fetch();
$userid = $row['id'];
$title = $_POST['title'];
$url = $_POST['url'];
$img = $_POST['image'];
$caption = $_POST['description'];

date_default_timezone_set("America/New_York");
$day = date("Y-m-d");

try {
$sql = 'INSERT INTO media SET
  userid = :userid,
  username = :username,
  title = :title,
  url = :url,
  mediaimg = :img,
  caption = :caption,
  date = :date';
  $s = $pdo->prepare($sql);
  $s->bindValue(':userid', $userid);
  $s->bindValue(':username', $username);
  $s->bindValue(':title', $title);
  $s->bindValue(':url', $url);
  $s->bindValue(':img', $img);
  $s->bindValue(':caption', $caption);
  $s->bindValue(':date', $day);
  $s->execute();

} catch (PDOException $e) {
  $error = 'Error posting.';
  include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
  exit();
}

$now = date("Y-m-d H:i:s");

try {
  $sql = 'UPDATE user 
  SET lastpost = :lastpost
  WHERE id = :id';
  $s = $pdo->prepare($sql);
  $s->bindValue(':id', $userid);
  $s->bindValue(':lastpost', $now);
  $s->execute();

} catch (PDOException $e) {
$error = 'Error adding post time.';
include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
exit();

}

echo json_encode($userid);

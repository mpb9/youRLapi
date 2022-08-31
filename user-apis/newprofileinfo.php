<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include $_SERVER['DOCUMENT_ROOT'] . '/youRLapi/includes/db.inc.php';
include $_SERVER['DOCUMENT_ROOT'] . '/youRLapi/includes/helpers.inc.php';


$restJson = file_get_contents("php://input");
$_POST = json_decode($restJson, true);

if (empty($_POST['name'])) die();

$name = $_POST['name'];
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$bio = $_POST['bio'];
$img = $_POST['img'];

try {
  $sql = 'UPDATE user SET 
      fullname = :fullname,
      email = :email,
      bio = :bio,
      img = :img
      WHERE name = :name';
  $s = $pdo->prepare($sql);
  $s->bindValue(':name', $name);
  $s->bindValue(':fullname', $fullname);
  $s->bindValue(':email', $email);
  $s->bindValue(':bio', $bio);
  $s->bindValue(':img', $img);
  $s->execute();

} catch (PDOException $e) {
  $error = 'Error updating user info.';
  echo $error;
}

$info[] = array(
  'email' => $email,
  'fullname' => $fullname,
  'bio' => $bio,
  'img' => $img
);

echo json_encode($info[0]);
// REALLY DONT HAVE TO RETURN... UPDATE IN FUTURE WHEN DONE TESTING
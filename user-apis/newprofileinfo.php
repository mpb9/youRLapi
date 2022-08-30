<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/db.inc.php';
include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/helpers.inc.php';


$restJson = file_get_contents("php://input");
$_POST = json_decode($restJson, true);

if (empty($_POST['name'])) die();

$name = $_POST['name'];
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$bio = $_POST['bio'];

try {
  $sql = 'UPDATE user SET 
      fullname = :fullname,
      email = :email,
      bio = :bio
      WHERE name = :name';
  $s = $pdo->prepare($sql);
  $s->bindValue(':name', $name);
  $s->bindValue(':fullname', $fullname);
  $s->bindValue(':email', $email);
  $s->bindValue(':bio', $bio);
  $s->execute();
}
catch (PDOException $e)
{
  $error = 'Error adding user img.';
  include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/error.html.php';
  exit();
}


  $info[] = array(
    'email' => $email,
    'fullname' => $fullname,
    'bio' => $bio,
    'img' => $_POST['img']
  );


echo json_encode($info[0]);

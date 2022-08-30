<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/db.inc.php';
include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/helpers.inc.php';


$restJson = file_get_contents("php://input");
$_POST = json_decode($restJson, true);

if (empty($_POST['name'])) die();

$name = $_POST['name'];

$incUser = $_POST['filtUser'];
$incTitle = $_POST['filtTitle'];
$incCaption = $_POST['filtCaption'];
$incSource = $_POST['filtSource'];
$search = $_POST['query'];

$searchLength = str_word_count($search);

// USER IS SEARCHING
if($searchLength > 0){
  
  include 'search.php';

  $posts[] = friend_search_result($name, $search, $incUser, $incTitle, $incSource, $incCaption);

  echo json_encode($posts[0]);

// USER NOT SEARCHING
} else {

try
{
  $sql = "SELECT * FROM media 
    JOIN follows
    ON media.username = follows.following
    WHERE follows.follower = :username
    ORDER BY media.mediaid DESC";
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
    'likes' => $row['likes'],
    'comments' => $row['comments'],
    'viewer' => $name,
    'date' => $row['date']
  );
  $num =1;
}

if($num !== 0) echo json_encode($posts);
else {
  $posts[] = array(
    'id' => 0,
    'poster' => 'no_friends_found', 
    'title' => 'Go follow someone!',
    'url' => 'https://www.webfx.com/blog/social-media/social-media-101/',
    'img' => 'https://simkl.net/fanart/66/6681208bf91de020_0.jpg',
    'caption' => 'No need to worry, add friends through searching or the popular page!',
    'likes' => 0,
    'comments' => 0,
    'viewer' => $name,
    'date' => date('Y-m-d')
  );
  echo json_encode($posts);
}
}
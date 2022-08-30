<?php
/*
$incUser = $_POST['filtUser'];
$incTitle = $_POST['filtTitle'];
$incCaption = $_POST['filtCaption'];
$incSource = $_POST['filtSource'];
$search = $_POST['query'];

      UNION
      SELECT * FROM media 
      WHERE :incSource AND url LIKE '%:search%'
      UNION
      SELECT * FROM media
      WHERE :incTitle AND title LIKE '%:search%'
      UNION
      SELECT * FROM media
      WHERE :incCaption AND caption LIKE '%:search%'

      $s->bindValue(':incTitle', $incTitle);
    $s->bindValue(':incSource', $incSource);
    $s->bindValue(':incCaption', $incCaption);
*/

function pop_search_result($name, $search, $incUser, $incTitle, $incSource, $incCaption){
  include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/db.inc.php';

  if($incUser) $user = true;
  else $user = false;

  try {
    $sql = "SELECT * FROM media 
      WHERE :incUser AND username LIKE '%".$search."%'
      UNION
      SELECT * FROM media 
      WHERE :incSource AND url LIKE '%".$search."%'
      UNION
      SELECT * FROM media
      WHERE :incTitle AND title LIKE '%".$search."%'
      UNION
      SELECT * FROM media
      WHERE :incCaption AND caption LIKE '%".$search."%' 
      ORDER BY date DESC, likes DESC";
    $s = $pdo->prepare($sql);
    $s->bindValue(':incUser', $user);
    $s->bindValue(':incTitle', $incTitle);
    $s->bindValue(':incSource', $incSource);
    $s->bindValue(':incCaption', $incCaption);
    $s->execute();

  }catch(PDOException $e) {
      $error = ($e->getMessage());
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
      'incUser' => $incUser,
      'search' => $search
    );
    $num =1;
  }
  
  if($num !== 0) return $posts;
  else {
    $posts[] = array(
      'id' => 0,
      'poster' => 'no_posts_found', 
      'title' => 'Alter your search!',
      'url' => 'https://www.webfx.com/blog/social-media/social-media-101/',
      'img' => 'https://simkl.net/fanart/66/6681208bf91de020_0.jpg',
      'caption' => 'No need to worry, just adjust your search!',
      'likes' => 0,
      'comments' => 0,
      'viewer' => $name
    );
    return $posts;
  }

}

function friend_search_result($name, $search, $incUser, $incTitle, $incSource, $incCaption){
  include $_SERVER['DOCUMENT_ROOT'] . '/mediashare/src/includes/db.inc.php';

  try {
    $sql = "SELECT * FROM media 
      JOIN follows
      ON media.username = follows.following
      WHERE follows.follower = :user
      AND ((:incUser AND media.username LIKE '%".$search."%')
        OR (:incSource AND media.url LIKE '%".$search."%')
        OR (:incTitle AND media.title LIKE '%".$search."%')
        OR (:incCaption AND media.caption LIKE '%".$search."%')) 
      ORDER BY date DESC, likes DESC";
    $s = $pdo->prepare($sql);
    $s->bindValue(':incUser', $incUser);
    $s->bindValue(':incTitle', $incTitle);
    $s->bindValue(':incSource', $incSource);
    $s->bindValue(':incCaption', $incCaption);
    $s->bindValue(':user', $name);
    $s->execute();

  }catch(PDOException $e) {
      $error = ($e->getMessage());
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
      'incUser' => $incUser,
      'search' => $search
    );
    $num =1;
  }
  
  if($num !== 0) return $posts;
  else {
    $posts[] = array(
      'id' => 0,
      'poster' => 'no_posts_found', 
      'title' => 'Alter your search!',
      'url' => 'https://www.webfx.com/blog/social-media/social-media-101/',
      'img' => 'https://simkl.net/fanart/66/6681208bf91de020_0.jpg',
      'caption' => 'No need to worry, just adjust your search!',
      'likes' => 0,
      'comments' => 0,
      'viewer' => $name
    );
    return $posts;
  }

}
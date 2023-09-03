<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require( '../config.php' );
require( '../init.php' );

//if($login_user){ //Only logged in user
$user_id = $login_user->id;
//if(isset($_POST['value']) && isset($_POST['ref'])){
$score = $_POST['value'];
//$score = base64_decode($score);
//$score = $score*1.33;

$game = $_POST['gameid'];
if($game) {
    $game_id = $game;
    $conn = open_connection();
    $sql = 'SELECT time_score FROM time_score WHERE user_id = :user_id AND game_id = :game_id LIMIT 1';
    $st = $conn->prepare($sql);
    $st->bindValue(":game_id", $game_id, PDO::PARAM_INT);
    $st->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $st->execute();
    $row = $st->fetch();
    if ($row) { //Update existing data
        //if ($row['score'] < $score) {
        $sql = 'UPDATE time_score SET time_score = time_score + :score WHERE user_id = :user_id AND game_id = :game_id LIMIT 1';
        $st = $conn->prepare($sql);
        $st->bindValue(":game_id", $game_id, PDO::PARAM_INT);
        $st->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $st->bindValue(":score", $score, PDO::PARAM_INT);
        $st->execute();
        //}
    } else {
        $sql = 'INSERT INTO time_score (game_id, user_id, time_score) VALUES ( :game_id, :user_id, :score)';
        $st = $conn->prepare($sql);
        $st->bindValue(":game_id", $game_id, PDO::PARAM_INT);
        $st->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $st->bindValue(":score", $score, PDO::PARAM_INT);
        $st->execute();
    }
//
    //$login_user->xp += 10;
    //$login_user->update_xp();
//
    echo 'ok';
}
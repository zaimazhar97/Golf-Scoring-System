<?php

include_once("../../ServiceProvider.php");

use php\database\Model;
use php\logic\Auth;
use php\logic\Sessions;
use php\misc\Helper;

$auth = new Auth;
$score = [];

$vid = $_GET['vid'];
$pid = $_GET['pid'];
$cid = $_GET['cid'];
$post_hole = $_POST['hole'];
$par = $_POST['par'];

$auth->check();
$auth->checkPrivilege(["superadmin", "admin"]);

if(Helper::checkRequest("GET"))
    Helper::denyAccess();

$sf = $auth->select("stableford", ["par", "point"], ["competition_id" => $cid])->getAll();
$sf_data = [];

foreach($sf as $data) {
    $sf_data += array($data['point'] => $data['par']);
}

$query = "";

for($i = 0; $i < count($_POST['hole']); $i++) {
    $par_val = $par[$i];
    $par_hole = $post_hole[$i];
    if(!empty($par_hole) && (strlen($par_val) > 0)) {
        $par_point = $auth->select("stableford", ["point"], ["competition_id" => $cid, "par" => $par_val])->get();
        array_push($score, [$vid, $pid, $par_hole, $par_val, $par_point['point'] ?? 0]);
    }
}

$inserScore = $auth->createMultiple("score", ["venue_id", "player_id", "hole", "par", "sf_point"], $score);

if($inserScore) {
    Sessions::setSession("par_insert", "Successfully inserted par");
    Helper::redirect("participant?cid=$cid&vid=$vid&pid=$pid");
} else {
    Sessions::setSession("par_insert", "Failed to insert par");
    Helper::redirect("participant?cid=$cid&vid=$vid&pid=$pid");
}

?>
<?php

include_once("../../ServiceProvider.php");

use php\logic\Auth;
use php\misc\Helper;
use php\logic\Sessions;

$auth = new Auth;

$auth->check();
$auth->checkPrivilege(["superadmin", "admin"]);

if(Helper::checkRequest("GET"))
    Helper::denyAccess();

$cid = $_GET['cid'];

$getStablefordPoint = $auth->select("stableford", ["point"], ["venue_id" => $_POST['venue'], "par" => $_POST['par']])->get();

$checkUpdate = $auth->update("score", ["par" => $_POST['par'], "sf_point" => $getStablefordPoint['point'] ?? 0],["venue_id" => $_POST['venue'], "hole" => $_POST['hole']]);

if($checkUpdate) {
    Sessions::setSession("par_edit", "Successfully edited par at hole " . $_POST['hole']);
    Helper::redirect('participant?pid=' . $_POST['player'] . '&vid=' . $_POST['venue'] . "&cid=$cid");
} else {
    Sessions::setSession("par_insert", "Failed editting par");
    Helper::redirect('participant');
}

?>
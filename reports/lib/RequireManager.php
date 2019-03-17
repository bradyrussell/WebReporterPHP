<?php
/**
 * Created by PhpStorm.
 * User: bruss5
 * Date: 3/12/2019
 * Time: 5:05 PM
 */

require_once "ReportsDatabase.php";

session_start();

if(!isset($_SESSION['m_id']) || is_null($_SESSION['m_id'])){
    header("Location:invalid_session.html");
    die("invalid_session");
}

$manager = GetManagerSessionByKey(GetDBConnection(), $_SESSION['m_id']);

if(is_null($manager) || $manager['session_key'] !== $_SESSION['m_id']){
    header("Location:invalid_session.html");
    die("expired_session");
}

echo("<!---- ".$manager['username']." -s_key ".$manager['session_key']." | _m_id = ".$_SESSION['m_id']."--->");
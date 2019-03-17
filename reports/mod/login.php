<?php
/**
 * Created by PhpStorm.
 * User: bruss5
 * Date: 3/12/2019
 * Time: 4:57 PM
 */

require_once "../lib/ReportsDatabase.php";

session_start();

if(isset($_GET['id'])){
    $_SESSION['m_id'] = $_GET['id'];
    header("Location:index.php");
    ClearOldManagerSessions(GetDBConnection());
    die("login");
} else {
    header("Location:invalid_session.html");
    die("invalid_session");
}
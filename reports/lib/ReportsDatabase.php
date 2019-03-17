<?php
/**
 * Created by PhpStorm.
 * User: bruss5
 * Date: 3/11/19
 * Time: too late
 */

// THIS MUST RESIDE OUTSIDE OF WEB ROOT////////////
// OTHERWISE CREDENTIALS ARE AT RISK///////////////
///////////////////////////////////////////////////
const Server = "localhost";////////////////////////
const Username = "user";/////////////
const Password = "pass";/////////////
const DBName = "webreporter";/////////////////
///////////////////////////////////////////////////

/* Connection to Database */
function GetDBConnection()
{
    $mysqli = new mysqli(Server, Username, Password, DBName);
    if ($mysqli->connect_error) {
        exit('Could not connect to database.'); //Should be a message a typical user could understand in production
    }
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $mysqli->set_charset("utf8mb4");
    return $mysqli;
}

/* End Connection to Database */

// public page exposed functions

function GetReportByKey($connection, $form_key){
    $stmt = $connection->prepare("SELECT * FROM reports WHERE form_key = ?");
    $stmt->bind_param("s", $form_key);

    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows !== 1) return null; //we expect there  to only be one result

    $row = $result->fetch_assoc(); //grab it as assoc array

    $stmt->close();
    return $row;
}

function CompleteReportByKey($connection, $form_key, $category, $details, $reporter_ip)
{ // returns true or false based on success
    $stmt = $connection->prepare("UPDATE reports SET category = ?, status = 'open', details = ?, form_key = '', reporter_ip = ? WHERE form_key = ?");
    $stmt->bind_param("ssss", $category, $details, $reporter_ip, $form_key);

    $stmt->execute();
    $rows = $stmt->affected_rows;
    $stmt->close();

    if ($rows === 1) {
        return true;
    } else {
        return false;
    }
}

function CancelReportByKey($connection, $form_key, $reporter_ip)
{ // returns true or false based on success
    $stmt = $connection->prepare("UPDATE reports SET status = 'cancelled', form_key = '', reporter_ip = ? WHERE form_key = ?");
    $stmt->bind_param("ss", $reporter_ip, $form_key);

    $stmt->execute();
    $rows = $stmt->affected_rows;
    $stmt->close();

    if ($rows === 1) {
        return true;
    } else {
        return false;
    }
}

function GetReportCategories($connection){
    $stmt = $connection->prepare("SELECT * FROM report_categories");
    $stmt->execute();

    $result = $stmt->get_result();

    $stmt->close();

    $out =array();

    while($row = $result->fetch_assoc()){
        $out[] = $row;
    }

    return $out;
}



///////////////////////////Moderator Panel Functions //////////////////////////////

function GetReportsByStatus($connection, $report_status){
    $stmt = $connection->prepare("SELECT * FROM reports WHERE status = ? ORDER BY report_id DESC");
    $stmt->bind_param("s", $report_status);

    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows === 0) return array(); //no results

    $stmt->close();


    $out =array();

    while($row = $result->fetch_assoc()){
        $out[] = $row;
    }

    return $out;
}

function GetReportsByManagerAndStatus($connection, $manager, $report_status){
    $stmt = $connection->prepare("SELECT * FROM reports WHERE manager = ? AND status = ? ORDER BY report_id DESC");
    $stmt->bind_param("ss", $manager, $report_status);

    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows === 0) return array(); //no results

    $stmt->close();


    $out =array();

    while($row = $result->fetch_assoc()){
        $out[] = $row;
    }

    return $out;
}

function GetManagerSessionByKey($connection, $key){
    $stmt = $connection->prepare("SELECT * FROM manager_sessions WHERE session_key = ? AND login_time >= NOW() - INTERVAL 1 HOUR");
    $stmt->bind_param("s", $key);

    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows !== 1) return null; //we expect there  to only be one result

    $row = $result->fetch_assoc(); //grab it as assoc array

    $stmt->close();
    return $row;
}

function ClearOldManagerSessions($connection){
    $stmt = $connection->prepare("UPDATE manager_sessions SET session_key = 'expired' WHERE login_time < NOW() - INTERVAL 1 HOUR");
    $stmt->execute();
    $rows = $stmt->affected_rows;
    $stmt->close();
    return $rows;
}

function GetReportByID($connection, $report_id){
    $stmt = $connection->prepare("SELECT * FROM reports WHERE report_id = ?");
    $stmt->bind_param("s", $report_id);

    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows !== 1) return null; //we expect there  to only be one result

    $row = $result->fetch_assoc(); //grab it as assoc array

    $stmt->close();
    return $row;
}

function ManageReportByID($connection, $report_number, $manager)
{ // returns true or false based on success
    $stmt = $connection->prepare("UPDATE reports SET manager = ?, status = 'in_progress' WHERE report_id = ?");
    $stmt->bind_param("ss", $manager,$report_number);

    $stmt->execute();
    $rows = $stmt->affected_rows;
    $stmt->close();

    if ($rows === 1) {
        return true;
    } else {
        return false;
    }
}

function AbandonReportByID($connection, $report_number)
{ // returns true or false based on success
    $stmt = $connection->prepare("UPDATE reports SET manager = '', status = 'open' WHERE report_id = ?");
    $stmt->bind_param("s",$report_number);

    $stmt->execute();
    $rows = $stmt->affected_rows;
    $stmt->close();

    if ($rows === 1) {
        return true;
    } else {
        return false;
    }
}

function GetReportsByKeyword($connection, $keyword, $show_closed){
    if($show_closed) {
        $stmt = $connection->prepare("SELECT * FROM reports WHERE CONCAT_WS('|',report_id,category,reported_username,reporter_username,reported_uuid,reporter_uuid,manager,resolution,reported_inv,reporter_ip) LIKE CONCAT('%',?,'%')");
    } else {
        $stmt = $connection->prepare("SELECT * FROM reports WHERE status IN ('open','in_progress','pending') AND CONCAT_WS('|',report_id,category,reported_username,reporter_username,reported_uuid,reporter_uuid,manager,resolution,reported_inv,reporter_ip) LIKE CONCAT('%',?,'%')");
    }


    $stmt->bind_param("s", $keyword);

    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows === 0) return array(); //no results

    $stmt->close();


    $out =array();

    while($row = $result->fetch_assoc()){
        $out[] = $row;
    }

    return $out;
}

function ResolveReportByID($connection, $report_number, $resolution)
{ // returns true or false based on success
    $stmt = $connection->prepare("UPDATE reports SET status = 'resolved', resolution = ? WHERE report_id = ?");
    $stmt->bind_param("ss",$resolution, $report_number );

    $stmt->execute();
    $rows = $stmt->affected_rows;
    $stmt->close();

    if ($rows === 1) {
        return true;
    } else {
        return false;
    }
}

function CreateReportCategory($connection, $category_name){
    $identifier = preg_replace('/\s+/', '', $category_name);

    $stmt = $connection->prepare("INSERT INTO report_categories (category_identifier, category_display_name) VALUES (?,?)");
    $stmt->bind_param("ss",$identifier, $category_name );

    $stmt->execute();
    $rows = $stmt->affected_rows;
    $stmt->close();

    if ($rows === 1) {
        return true;
    } else {
        return false;
    }
}

function DeleteReportCategory($connection, $identifier){
    $stmt = $connection->prepare("DELETE FROM report_categories WHERE category_identifier = ?");
    $stmt->bind_param("s",$identifier);

    $stmt->execute();
    $rows = $stmt->affected_rows;
    $stmt->close();

    if ($rows === 1) {
        return true;
    } else {
        return false;
    }
}

function SetReportCategoryContents($connection, $identifier, $subcategories){
    $stmt = $connection->prepare("UPDATE report_categories SET subcategories = ? WHERE category_identifier = ?");
    $stmt->bind_param("ss",$subcategories,$identifier );

    $stmt->execute();
    $rows = $stmt->affected_rows;
    $stmt->close();

    if ($rows === 1) {
        return true;
    } else {
        return false;
    }
}

/* Begin Util Functions */

function LocationToDynmapLink($location, $map_url = "http://jahcraft.vip/map/"){
    $pieces = explode(":", $location);
    $world = $pieces[0];
    $x = floor($pieces[1]);
    $y = floor($pieces[2]);
    $z = floor($pieces[3]);

    $map_link = "$map_url?worldname=$world&mapname=surface&zoom=8&x=$x&y=$y&z=$z";
    return $map_link;
}

function UUIDtoAvatar($uuid){
    if($uuid === "offline") return "https://cdn1.iconfinder.com/data/icons/hawcons/32/698378-icon-131-cloud-error-32.png";
    return "https://crafatar.com/avatars/$uuid?size=32&default=MHF_Steve&overlay";
}

function UUIDtoFullBody($uuid){
    if($uuid === "offline") return "https://cdn1.iconfinder.com/data/icons/hawcons/32/698378-icon-131-cloud-error-128.png";
    return "https://crafatar.com/renders/body/$uuid?size=128&default=MHF_Steve&overlay";
}


<?php
/**
 * Created by PhpStorm.
 * User: bruss5
 * Date: 3/15/2019
 * Time: 7:12 PM
 */

const DefaultLogsDir = "/home/ubuntu/logs";


function getRelevantLog($time_string, $LogsDir = DefaultLogsDir)
{
    $time_parts = explode(" ", $time_string);
    $relevant_logs = array("latest.log");

    foreach (scandir($LogsDir) as $file) {
        if (substr($file, 0, strlen($time_parts[0])) === $time_parts[0]) {
            $relevant_logs[] = $file;
        }
    }

    foreach ($relevant_logs as $log) {
        $log_contents = gzfile($LogsDir . DIRECTORY_SEPARATOR . $log);
        foreach ($log_contents as $line => $log_content) {
            $search = "[" . $time_parts[1];
            if (substr($log_content, 0, strlen($search)) === $search) {
                return array("line"=>$line, "text"=>$log_contents);
            }
        }
    }

    // still nothing? check latest.log

    //file($filename, FILE_IGNORE_NEW_LINES);
    return null;
}

$LOG = getRelevantLog($_GET['time']);

?>

<b>See: <?php echo($LOG['text'][$LOG['line']]);?></b>
<select style="height: 95%;width: 99%" multiple><?php foreach ($LOG['text'] as $log_line){
    echo("<option>$log_line</option>");
}?></select>


<?php
/**
 * Created by PhpStorm.
 * User: bruss5
 * Date: 3/12/2019
 * Time: 6:36 PM
 */

require_once "../lib/RequireManager.php";

if(!isset($_GET['id']) || empty($_GET['id'])){
    header("Location:index.php");
    die("Invalid token.");
} else {
    $report = GetReportByID(GetDBConnection(), $_GET['id']);

    if (is_null($report)) {
        header("Location:invalid_key.html");
        die("Invalid token.");
    }
}

if(isset($_GET['manage_ticket']) && $_GET['manage_ticket'] == 1){
    ManageReportByID(GetDBConnection(), $_GET['id'], $manager['username']);
    $report = GetReportByID(GetDBConnection(), $_GET['id']);
}

if($report['manager'] === $manager['username'] && isset($_GET['abandon_ticket']) && $_GET['abandon_ticket'] == 1){
    AbandonReportByID(GetDBConnection(), $_GET['id']);
    $report = GetReportByID(GetDBConnection(), $_GET['id']);
}


?>
<link rel="stylesheet" href="../css/bootstrap.min.css">


<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">JahCraft.VIP Reports Manager</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <?php
            if($report['status'] !== "cancelled" && $report['status'] !== "resolved" && $report['status'] !== "pending") {
                if ($report['manager'] === $manager['username']) {
                    echo("
                    <form class=\"navbar-form navbar-right\" role=\"form\" method=\"post\" action=\"resolve.php\">
                        <input type=\"hidden\" name=\"id\" value=\"" . $_GET['id'] . "\"/>
                        <button class=\"btn btn-success\" type=\"submit\" >Resolve Report</button>
                    </form>
                    <form class=\"navbar-form navbar-right\" role=\"form\" method=\"get\" action=\"\">
                        <input type=\"hidden\" name=\"id\" value=\"" . $_GET['id'] . "\"/>
                        <button class=\"btn btn-danger\" type=\"submit\" name=\"abandon_ticket\" value=\"1\">Abandon Report</button>
                    </form>");
                } else {
                    echo("            <form class=\"navbar-form navbar-right\" role=\"form\" method=\"get\" action=\"\">
                        <input type=\"hidden\" name=\"id\" value=\"" . $_GET['id'] . "\"/>
                        <button class=\"btn btn-primary\" type=\"submit\" name=\"manage_ticket\" value=\"1\">Manage This Report</button>
                    </form>");
                }
            }
            ?>

            <form class="navbar-form" role="form" method="get" action="queue.php">
                <button type="submit" class="btn btn-warning">My Queue</button>
            </form>

        </div><!--/.navbar-collapse -->
    </div>
</nav>
<br/><br/><br/>

<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">Report #<?php echo($report['report_id']);?> Details</th>
    </tr>
  </thead>
  <tbody>

  <tr>
      <th scope="row">Status</th>
      <td><?php echo($report['status']);?></td>
  </tr>

  <tr>
      <th scope="row">Report Manager</th>
      <td><?php echo($report['manager']);?></td>
  </tr>

    <tr>
      <th scope="row">Reporter</th>
      <td><button class="btn btn-info" type="button" onclick="copyToClipboard('/tprp <?php echo($report['report_id']);?> reporter')">Copy Teleport</button> <button class="btn btn-info" type="button" onclick="copyToClipboard('<?php echo($report['reporter_username']);?>')">Copy Username</button> <?php echo($report['reporter_username'] . " | <a class='' href='" . LocationToDynmapLink($report['reporter_location']) . "'>Location</a> | " . $report['reporter_uuid']. " <img src='" . UUIDtoAvatar($report['reporter_uuid']). "'/> " . " | " . $report['reporter_ip']);?></td>
    </tr>

    <tr>
        <th scope="row">Reported Player</th>
        <td><button class="btn btn-info" type="button" onclick="copyToClipboard('/tprp <?php echo($report['report_id']);?> reported')">Copy Teleport</button> <button class="btn btn-info" type="button" onclick="copyToClipboard('<?php echo($report['reported_username']);?>')">Copy Username</button> <?php echo($report['reported_username'] . " | <a href='" . LocationToDynmapLink($report['reported_location']) . "'>Location</a>  | " . $report['reported_uuid']. " <img src='" . UUIDtoAvatar($report['reported_uuid']). "'/> ");?></td>
    </tr>

    <tr>
        <th scope="row">Report Time</th>
        <td><?php echo($report['report_time']);?></td>
    </tr>

  <tr>
      <th scope="row">Category</th>
      <td><?php echo($report['category']);?></td>
  </tr>

  <tr>
      <th scope="row">Details</th>
      <td><?php echo($report['details']);?></td>
  </tr>

  <tr>
      <th scope="row">Reported Player's Inventory</th>
      <td><textarea rows="2" cols="32" readonly><?php echo($report['reported_inv']);?></textarea></td>
  </tr>

  <tr>
      <th scope="row">Resolution</th>
      <td><?php echo($report['resolution']);?></td>
  </tr>

  <tr>
      <th scope="row">Server Logs</th>
      <td><form action="logs.php" method="get"><button class="btn btn-primary" type="submit" name="time" value="<?php echo($report['report_time']);?>">Search Server Logs</button></form> </td>
  </tr>

  </tbody>
</table>
<br/>
<script>
    const copyToClipboard = str => {//https://hackernoon.com/copying-text-to-clipboard-with-javascript-df4d4988697f
        const el = document.createElement('textarea');  // Create a <textarea> element
        el.value = str;                                 // Set its value to the string that you want copied
        el.setAttribute('readonly', '');                // Make it readonly to be tamper-proof
        el.style.position = 'absolute';
        el.style.left = '-9999px';                      // Move outside the screen to make it invisible
        document.body.appendChild(el);                  // Append the <textarea> element to the HTML document
        const selected =
            document.getSelection().rangeCount > 0        // Check if there is any content selected previously
                ? document.getSelection().getRangeAt(0)     // Store selection if found
                : false;                                    // Mark as false to know no selection existed before
        el.select();                                    // Select the <textarea> content
        document.execCommand('copy');                   // Copy - only works as a result of a user action (e.g. click events)
        document.body.removeChild(el);                  // Remove the <textarea> element
        if (selected) {                                 // If a selection existed before copying
            document.getSelection().removeAllRanges();    // Unselect everything on the HTML document
            document.getSelection().addRange(selected);   // Restore the original selection
        }
    };</script>
<footer style="position:fixed;bottom:0;left:0;right:0;height:60px;" class="panel-footer">
    <p class="panel-footer">WebReporter Copyright Brady Russell 2019. Thank you to <a href="https://crafatar.com">Crafatar</a> for providing avatars.</p>
</footer>
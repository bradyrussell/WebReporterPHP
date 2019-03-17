<?php
/**
 * Created by PhpStorm.
 * User: bruss5
 * Date: 3/12/2019
 * Time: 9:26 AM
 */

require_once "../lib/RequireManager.php";

const DEBUG_MODE = true;

const StatusColors = array("cancelled"=>"red", "pending"=>"orange", "open"=>"green", "in_progress"=>"blue","resolved"=>"purple");

$show_closed = $_GET['closed'];

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
            <form class="navbar-form navbar-right" role="form" method="get" action="search.php">
                <div class="form-group">
                    <input name="query" type="text" placeholder="Search Reports..." class="form-control">
                </div>
                <button type="submit" class="btn btn-default">Search</button>
            </form>

            <form class="navbar-form navbar-right" role="form" method="get" action="">
                <div class="form-group">
                    <label class="label">Show Closed Reports:
                        <select class="form-control" name="closed">
                            <option <?php if(!$show_closed) echo("selected");?> value="0">No</option>
                            <option <?php if($show_closed) echo("selected");?> value="1">Yes</option>
                        </select>
                    </label>
                </div>
                <button type="submit" class="btn btn-success">Set</button>
            </form>

            <form class="navbar-form navbar-right" role="form" method="get" action="categories.php">
                <button type="submit" class="btn btn-primary">Edit Report Categories</button>
            </form>

            <form class="navbar-form" role="form" method="get" action="queue.php">
                <button type="submit" class="btn btn-warning">My Queue</button>
            </form>
        </div><!--/.navbar-collapse -->
    </div>
</nav>
<br/><br/><br/>

<table class="table table-striped table-dark table-hover">
    <thead>
    <tr>
        <th scope="col">Report #</th>
        <th scope="col">Status</th>
        <th scope="col">Time</th>
        <th scope="col">Category</th>
        <th scope="col">Reported User</th> <!--- show uuid & location on mouseover --->
        <th scope="col">Reporting User</th>
        <th scope="col">Reporting IP</th>
        <th scope="col">Report Manager</th>
    </tr>
    </thead>
    <tbody>
    <form action="report.php" method="get">
        <?php

        $tickets = array_merge(GetReportsByStatus(GetDBConnection(), "open"), GetReportsByStatus(GetDBConnection(), "in_progress"), GetReportsByStatus(GetDBConnection(), "pending"));

        if($show_closed > 0){
            $tickets = array_merge($tickets, GetReportsByStatus(GetDBConnection(), "resolved"), GetReportsByStatus(GetDBConnection(), "cancelled"));
        }

        foreach ($tickets as $ticket){
            echo("<tr>");
            echo("<th scope=\"row\"><input class='btn-sm btn-info' type='submit' name='id' value='".$ticket['report_id']."'/>  </th>");
            echo("<td style='color: ".StatusColors[$ticket['status']]."'>".$ticket['status']."</td>");
            echo("<td>".$ticket['report_time']."</td>");
            echo("<td><span title='".$ticket['details']."'>".$ticket['category']."</span></td>");
            echo("<td><span title='".$ticket['reported_uuid']."'><img src='".UUIDtoAvatar($ticket['reported_uuid'])."'/> ".$ticket['reported_username']."</span></td>");
            echo("<td><span title='".$ticket['reporter_uuid']."'><img src='".UUIDtoAvatar($ticket['reporter_uuid'])."'/> ".$ticket['reporter_username']."</span></td>");
            echo("<td>".$ticket['reporter_ip']."</td>");
            echo("<td>".$ticket['manager']."</td>");
            echo("</tr>");
        }

        ?>
    </form>
    </tbody>
</table>


<footer style="position:fixed;bottom:0;left:0;right:0;height:60px;" class="panel-footer">
    <p class="panel-footer">WebReporter Copyright Brady Russell 2019. Thank you to <a href="https://crafatar.com">Crafatar</a> for providing avatars.</p>
</footer>
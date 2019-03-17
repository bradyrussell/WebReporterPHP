<?php
/**
 * Created by PhpStorm.
 * User: bruss5
 * Date: 3/11/2019
 * Time: 8:16 PM
 */

require_once "../lib/RequireManager.php";

const DEBUG_MODE = false;

if((isset($_POST['id']) && !empty($_POST['id'])) && (isset($_POST['resolution']) && !empty($_POST['resolution']))){
    ResolveReportByID(GetDBConnection(), $_POST['id'],$_POST['resolution']);
    header("Location:report.php?id=".$_POST['id']);
    die("resolved");
}

if(!isset($_POST['id']) || empty($_POST['id'])){
    header("Location:invalid_key.html");
    die("Invalid token.");
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Resolve Report</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>

        /** {*/
            /*line-height: 1.2;*/
            /*margin: 0;*/
        /*}*/

        html {
            color: #888;
            display: table;
            font-family: sans-serif;
            height: 100%;
            text-align: center;
            width: 100%;
        }

        body {
            display: table-cell;
            vertical-align: middle;
            margin: 2em auto;
        }

        h1 {
            color: #555;
            font-size: 2em;
            font-weight: 400;
        }

        p {
            margin: 0 auto;
            width: 280px;
        }

        @media only screen and (max-width: 280px) {

            body, p {
                width: 95%;
            }

            h1 {
                font-size: 1.5em;
                margin: 0 0 0.3em;
            }

        }

    </style>
</head>
<body class="media-body" style="background-color:lightblue">
    <h1>Resolve Report</h1><br/>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $_POST['id'];?>"/>

        <div class="form-group row">
            <img src="<?php echo(UUIDtoFullBody(GetReportByID(GetDBConnection(), $_POST['id'])['reported_uuid'])); ?>"/>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Report Resolution: </label>
            <div class="col-sm-10">
                <textarea placeholder="How have you resolved the issue?" id="report" name="resolution" class="form-horizontal" rows="8" cols="120"></textarea><p class="text-muted" id="count">Characters: 0</p>
            </div>
        </div>

        <div class="form-group row">
            <input type="submit" id="submitbtn" class="btn-lg btn-success" value="Resolve Report"  name="action"/>
        </div>

    </form>
</body>
</html>
<script>
    document.getElementById('report').onkeyup = function () {
        document.getElementById('count').innerHTML = "Characters: " + this.value.length;
    };
</script>
<!-- IE needs 512+ bytes: http://blogs.msdn.com/b/ieinternals/archive/2010/08/19/http-error-pages-in-internet-explorer.aspx -->
<footer style="position:fixed;bottom:0;left:0;right:0;height:60px;" class="panel-footer">
    <p style="width: 90%" class="panel-footer">WebReporter Copyright Brady Russell 2019. Thank you to <a href="https://crafatar.com">Crafatar</a> for providing avatars.</p>
</footer>
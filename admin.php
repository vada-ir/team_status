<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);
$db = new SQLite3('db/devstat.sqlite');
$now = date('Y-m-d H:i:s', time());
$string = file_get_contents("config.json");
$configs = json_decode($string, true);
//first auth time..
if (isset($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/Basic\s+(.*)$/i', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
    list($name, $password) = explode(':', base64_decode($matches[1]));
    $_SERVER['PHP_AUTH_USER'] = strip_tags($name);
    $_SERVER['PHP_AUTH_PW'] = strip_tags($password);
    $user = $_SERVER['PHP_AUTH_USER'];
    $pass = $_SERVER['PHP_AUTH_PW'];
    if($user != 'dev' || $pass != 'dev@status')
    {   // error
        header('WWW-Authenticate: Basic realm="Vada-WEB"');
        header('HTTP/1.0 401 Unauthorized');
        die(':P');
    }
}
else
{
    header('WWW-Authenticate: Basic realm="Dev-Team"');
    header('HTTP/1.0 401 Unauthorized');
    die(':)))');
}

if (isset($_POST['state'])) {
    $start = $_POST['date'];
    $duration = $_POST['time'];
    $type = $_POST['state'];
    $description = $_POST['description'];

    // find unterminated state and finish it if exists!

    //row count
    $rows = $db->query("SELECT COUNT(*) as count FROM status WHERE end_date IS NULL");
    $row = $rows->fetchArray();
    $numRows = $row['count'];

    if($numRows > 0)
    {
        $query = "SELECT * FROM status WHERE end_date IS NULL ORDER BY id LIMIT 1";
        $res = $db->query($query);

        $UnterminatedState = $res->fetchArray(SQLITE3_ASSOC);
        $q = "UPDATE status SET end_date = '$now' WHERE id = " . $UnterminatedState['id'];
        $res = $db->exec($q);
    }

    // add new status
    $q = "INSERT INTO status VALUES(null, '$type', '$now', null, '$type', '$description', $duration) ";
    $res = $db->exec($q);
}

// find unterminated state and tab color
//row count
$rows = $db->query("SELECT COUNT(*) as count FROM status WHERE end_date IS NULL");
$row = $rows->fetchArray();
$numRows = $row['count'];

if($numRows > 0)
{
    $query = "SELECT * FROM status WHERE end_date IS NULL ORDER BY id LIMIT 1";
    $res = $db->query($query);
    $UnterminatedState = $res->fetchArray(SQLITE3_ASSOC);
    $statusTabColor = $configs[$UnterminatedState['type']]['color'];
}

?>
<h1>Dev team Status</h1>
<div role="tabpanel" class="well">

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#status" aria-controls="status" role="tab" data-toggle="tab">Status</a>
        </li>
        <li role="presentation"><a href="#history" aria-controls="history" role="tab" data-toggle="tab">History</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade in active panel panel-default" id="status"
             style="background-color: <?php if (isset($statusTabColor)) echo $statusTabColor; ?>"
            >
            <div class="panel-body">

                <!-- status buttons-->
                <?php
                foreach ($configs as $key => $conf) {
                    if ($conf['enable'])
                        echo ' <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newState"
                        data-title="' . $conf['title'] . '" data-time="' . $conf['time'] . '"  id="' . $key . '" >' . $conf['title'] . '</button>';
                }

                ?>



                <div class="modal fade" id="newState" tabindex="-1" role="dialog"
                     aria-labelledby="newStateLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form class="form-horizontal" method="post" action="/admin">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="newStateLabel">New message</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="form-group">
                                        <label for="date" class="col-sm-3 control-label">Date:</label>

                                        <div class="col-sm-9">
                                            <input type="text" id="date" name="date"
                                                   value="<?php echo date('Y-m-d H:i:s', time()); ?>"
                                                   readonly="readonly">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="message-text" class="col-sm-3 control-label">New state:</label>

                                        <div class="col-sm-9">
                                            <input type="text" id="state" name="state" readonly="readonly">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="message-text" class="col-sm-3 control-label">Planed time:</label>

                                        <div class="col-sm-2">
                                            <input type="text" name="time" class="form-control" id="time">
                                        </div>

                                        <div class="col-sm-2">
                                            <h5 id="min">min</h5>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="message-text" class="col-sm-3 control-label">Description:</label>

                                        <div class="col-sm-9">
                                            <textarea name="description" class="form-control"
                                                      id="description"></textarea>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Let's go!">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (isset($UnterminatedState)): ?>
                    <div class="voffset6">
                        <h5><strong>Date :</strong> <?php echo  date('Y-m-d',strtotime($UnterminatedState['start_date'])); ?></h5>
                        <h5><strong>Current state :</strong> <?php echo $UnterminatedState['type']; ?> </h5>
                        <h5><strong>Started at :</strong> <?php echo  date('H:i',strtotime($UnterminatedState['start_date'])); ?> </h5>
                        <h5><strong>Planed close time :</strong> <?php echo $UnterminatedState['duration']; ?> min </h5>
                        <h5><strong>Description :</strong> <?php echo $UnterminatedState['description']; ?></h5>
                    </div>
                <?php else: ?>
                    <div class="voffset6">
                        <h3><strong>Idle...</strong></h3>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <?php

        $q = "SELECT * FROM status ORDER BY id DESC ";
        $res = $db->query($q);

        $result = [];
        while ($row = $res->fetchArray(SQLITE3_ASSOC))
            $result[] = $row;

        $rowCount = count($result);
        ?>

        <div role="tabpanel" class="tab-pane fade panel panel-default" id="history">
            <div class="panel-body">

                <?php

                if ($rowCount > 0) {
                    echo '<div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr class="danger">
                      <th>Date</th>
                      <th>Status</th>
                      <th>Start</th>
                      <th>End</th>
                      <th>Duration</th>
                      <th>Planned</th>
                      <th>Description</th>
                    </tr>
                  </thead>
                  <tbody>';

                    foreach ($result as $row) {
                        if ($row['end_date']) {
                            $end_date = date('H:i', strtotime($row['end_date']));
                            $duration = strtotime($row['end_date']) - strtotime($row['start_date']);
                            $minutes = round($duration / 60);
                            $duration = $minutes.' min';
                        } else {
                            $end_date = $duration = '-';
                        }
                        if($end_date == '-')
                            echo '<tr class="success" >';
                        else
                            echo '<tr class="" >';

                        echo
                            '<td>' . date('Y-m-d', strtotime($row['start_date'])) . '</td>' .
                            '<td>' . $row['type'] . '</td>' .
                            '<td>' . date('H:i', strtotime($row['start_date'])) . '</td>' .
                            '<td>' . $end_date . '</td>' .
                            '<td>' . $duration . '</td>' .
                            '<td>' . $row['duration'] . ' min </td>' .
                            '<td>' . $row['description'] . '</td>' .
                            '</tr>';
                    }

                    echo "</tbody></table></div>";

                }
                ?>

            </div>
        </div>
    </div>

</div>


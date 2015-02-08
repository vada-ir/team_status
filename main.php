<?php

$db = new SQLite3('db/devstat.sqlite');
$now = date('Y-m-d H:i:s', time());
$string = file_get_contents("config.json");
$configs = json_decode($string, true);

// find unterminated state and tab color
//row count
$rows = $db->query("SELECT COUNT(*) as count FROM status WHERE end_date IS NULL");
$row = $rows->fetchArray();
$numRows = $row['count'];

if ($numRows > 0) {
    $query = "SELECT * FROM status WHERE end_date IS NULL ORDER BY id LIMIT 1";
    $res = $db->query($query);
    $UnterminatedState = $res->fetchArray(SQLITE3_ASSOC);
    $statusTabColor = $configs[$UnterminatedState['type']]['color'];
    $fontColor = $configs[$UnterminatedState['type']]['font_color'];
    $spent = round(strtotime($now) - strtotime($UnterminatedState['start_date']))/60;
    $plannedTime = $configs[$UnterminatedState['type']]['time'];
    $extraTime = round(strtotime($now) - strtotime("+$plannedTime minute", strtotime($UnterminatedState['start_date'])))/60;
    $countDown = $plannedTime - $spent;
    if($countDown<0)
        $countDown = 0;
    echo    '<script>status_duration ='.($countDown*60).'; var reloadPage = true; extraTime ='.($extraTime*60).'; </script>';
    echo "<script>$('body').css('background-color', '$statusTabColor');</script>";

}
?>

<div class="countdown"></div>

<?php if (isset($UnterminatedState)): ?>
    <div class="voffset6" style="color: <?php echo $fontColor; ?> ;">
        <h3 class="animated shake" ><strong>Current state :</strong><em class="current_status"><?php echo $configs[$UnterminatedState['type']]['title']; ?> </em></h3>
        <h5><strong>Date :</strong> <?php echo date('Y-m-d',strtotime($UnterminatedState['start_date'])); ?></h5>
        <h5><strong>Started at :</strong> <?php echo date('H:i',strtotime($UnterminatedState['start_date'])); ?> </h5>
        <h5><strong>Planed close time :</strong> <?php echo $UnterminatedState['duration']; ?> min </h5>
        <h5><strong>Description :</strong> <?php echo $UnterminatedState['description']; ?></h5>
    </div>
<?php else: ?>
    <div class="voffset6">
        <h3><strong>Idle...</strong></h3>
    </div>
<?php endif; ?>
<button class="btn btn-success" onclick="location.reload();">Refresh</button>



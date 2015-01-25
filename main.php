<?php


$db = new SQLite3('devstat.sqlite');

$now = date('Y-m-d H:i:s', time());

$q = "SELECT * FROM status where end_date > '$now'";
$res = $db->query($q);

$result = [];
while( $row = $res->fetchArray(SQLITE3_ASSOC) )
    $result[] = $row;

$rowCount = count($result);

if($rowCount > 0)
{

    echo "<h1>Sorry, We're busy.</h1>";
    echo "<h3>Please check our status below </h3>";
    echo "<script>$('body').css('background-color', 'red');</script>";
    echo '<div class="table-responsive">
    <table class="table table-striped">
    <thead>
        <tr class="danger">
          <th>Subject</th>
          <th>Start</th>
          <th>End</th>
          <th>Type</th>
          <th>Description</th>
        </tr>
      </thead>
      <tbody>';

    foreach ($result as $row) {
        echo '<tr class="danger">' . '<td>'.$row['subject']. '</td>' . 
        '<td>'.$row['start_date']. '</td>' .
        '<td>'.$row['end_date']. '</td>' .
        '<td>'.$row['type']. '</td>' .
        '<td>'.$row['description']. '</td>' .
        '</tr>';
    }

    echo "</tbody></table></div>";

}
else
{
    echo "<h1>Hooray, We're free.</h1>";
    echo "<script>$('body').css('background-color', 'white');</script>";
}



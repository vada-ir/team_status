<?php

if(isset($_POST['subject']))
{
    $subject = $_POST['subject'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $type = $_POST['type'];
    $description = $_POST['description'];

    $db = new SQLite3('devstat.sqlite');
    $q = "INSERT INTO status VALUES(null, '$subject', '$start', '$end', '$type', '$description') ";
    $res = $db->exec($q);
}

echo "admin";
?>

<form action="/admin" method="post">
  <div class="form-group">
    <label for="exampleInputEmail1">Subject</label>
    <input type="text" class="form-control" id="subject" placeholder="Enter email" name="subject">
</div>
<div class="form-group">
    <label for="exampleInputPassword1">Start</label>
    <input type="text" class="form-control" id="start_date" placeholder="Password" name="start_date">
</div>
<div class="form-group">
    <label for="exampleInputPassword1">End</label>
    <input type="text" class="form-control" id="end_date" placeholder="Password" name="end_date">
</div>
<div class="form-group">
    <label for="exampleInputPassword1">Type</label>
    <input type="text" class="form-control" id="type" placeholder="Password" name="type">
</div>
<div class="form-group">
    <label for="exampleInputPassword1">Description</label>
    <input type="text" class="form-control" id="description" placeholder="Password" name="description">
</div>

<button type="submit" class="btn btn-default">Add</button>
</form>
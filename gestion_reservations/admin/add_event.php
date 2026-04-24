<?php
require "../config.php";

if(isset($_POST["add"])) {

$stmt=$pdo->prepare("INSERT INTO events(title,date_event,location,nbPlaces,price)
VALUES(?,?,?,?,?)");

$stmt->execute([
$_POST["title"],
$_POST["date"],
$_POST["location"],
$_POST["places"],
$_POST["price"]
]);

header("Location:dashboard.php");
}
?>

<form method="POST">
<input name="title"><br>
<input type="date" name="date"><br>
<input name="location"><br>
<input type="number" name="places"><br>
<input type="number" name="price"><br>
<button name="add">Add</button>
</form>
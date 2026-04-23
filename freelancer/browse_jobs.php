<?php include("../db.php"); ?>

<h2>Jobs</h2>

<?php
$res=$conn->query("SELECT * FROM jobs");

while($row=$res->fetch_assoc()){
echo $row['title'];
echo "<a href='apply.php?id=".$row['id']."'> Apply</a><br>";
}
?>
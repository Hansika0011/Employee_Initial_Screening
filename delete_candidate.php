<?php
$conn = new mysqli("localhost", "root", "", "talentscout");
if ($conn->connect_error) die("Connection failed");

$id = intval($_GET['id']);
$conn->query("DELETE FROM candidates WHERE id = $id");
$conn->close();
header("Location: admin_dashboard.php");
exit();
?>

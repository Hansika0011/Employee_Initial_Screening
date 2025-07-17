<?php
$conn = new mysqli("localhost", "root", "", "talentscout");
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $tech_stack = $conn->real_escape_string($_POST['tech_stack']);

    $conn->query("UPDATE candidates SET name='$name', tech_stack='$tech_stack' WHERE id=$id");
}
$conn->close();
header("Location: admin_dashboard.php");
exit();
?>

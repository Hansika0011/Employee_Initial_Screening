<?php
$conn = new mysqli("localhost", "root", "", "talentscout");
$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM candidates WHERE id = $id");
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Candidate - TalentScout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        a.back {
            display: block;
            margin-top: 15px;
            text-align: center;
            color: #3498db;
            text-decoration: none;
        }

        a.back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Candidate</h2>
    <form method="post" action="update_candidate.php">
        <input type="hidden" name="id" value="<?= $data['id'] ?>">

        <label for="name">Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>">

        <label for="tech_stack">Tech Stack:</label>
        <input type="text" name="tech_stack" value="<?= htmlspecialchars($data['tech_stack']) ?>">

        <!-- Add more fields as needed -->

        <button type="submit">Update Candidate</button>
        <a href="admin_dashboard.php" class="back">← Back to Dashboard</a>
    </form>
</div>

</body>
</html>

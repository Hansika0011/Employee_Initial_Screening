<?php

$conn = new mysqli("localhost", "root", "", "talentscout");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Encryption key (should be kept safe!)
define('ENCRYPTION_KEY', 'your_secret_key_here');

function encryptData($data) {
    $key = ENCRYPTION_KEY;
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($data, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
    return base64_encode($iv . $hmac . $ciphertext_raw);
}

// Step 1: Get duplicate auth_ids
$duplicates = $conn->query("
    SELECT auth_id 
    FROM candidates 
    GROUP BY auth_id 
    HAVING COUNT(*) > 1
");

while ($dup = $duplicates->fetch_assoc()) {
    $auth_id = $dup['auth_id'];

    // Step 2: Fetch the two rows
    $stmt = $conn->prepare("SELECT * FROM candidates WHERE auth_id = ?");
    $stmt->bind_param("i", $auth_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    if (count($rows) !== 2) continue;

    $merged = [];
    foreach ($rows[0] as $key => $value) {
        $merged[$key] = !empty($rows[0][$key]) ? $rows[0][$key] : $rows[1][$key];
        if (empty($merged[$key]) && !empty($rows[1][$key])) {
            $merged[$key] = $rows[1][$key];
        }
    }

    // Encrypt sensitive fields
    $merged['phone'] = encryptData($merged['phone']);
    $merged['tech_stack'] = encryptData($merged['tech_stack']);
    $merged['answers'] = encryptData($merged['answers']);
    $merged['feedback'] = encryptData($merged['feedback']);
    $merged['score'] = encryptData($merged['score']);

    // Step 3: Keep first row, update with merged data
    $id_to_keep = $rows[0]['id'];
    $id_to_delete = $rows[1]['id'];

    $stmt_update = $conn->prepare("
        UPDATE candidates SET name=?, phone=?, experience=?, position=?, location=?, tech_stack=?, 
        answers=?, feedback=?, score=?, submission_status=?, evaluation_status=?, feedback_status=? 
        WHERE id = ?
    ");
    $stmt_update->bind_param(
        "ssisssssssssi",
        $merged['name'],
        $merged['phone'],
        $merged['experience'],
        $merged['position'],
        $merged['location'],
        $merged['tech_stack'],
        $merged['answers'],
        $merged['feedback'],
        $merged['score'],
        $merged['submission_status'],
        $merged['evaluation_status'],
        $merged['feedback_status'],
        $id_to_keep
    );
    $stmt_update->execute();

    // Step 4: Delete the second row
    $stmt_delete = $conn->prepare("DELETE FROM candidates WHERE id = ?");
    $stmt_delete->bind_param("i", $id_to_delete);
    $stmt_delete->execute();
}

echo "✅ Merging and encryption complete.";

$conn->close();
?>

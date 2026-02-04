<?php
// test_session.php - Place this in your root directory
session_start();

echo "<h2>Session Debug Info</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . (isset($_SESSION['user_id']) ? "LOGGED IN" : "NOT LOGGED IN") . "\n\n";
echo "Session Data:\n";
print_r($_SESSION);
echo "</pre>";

if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✅ Session is working! User ID: " . $_SESSION['user_id'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ No user_id in session. Please log in first.</p>";
}
?>
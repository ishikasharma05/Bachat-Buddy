<?php
// impulse_detector.php - Detect Impulse Spending
// Checks if the user is spending more than usual

function detectImpulse($conn, $user_id) {
    // Last 24 hours spending count and amount
    $q = $conn->prepare("
        SELECT COUNT(*), IFNULL(SUM(amount), 0)
        FROM transactions
        WHERE user_id = ?
        AND type = 'expense'
        AND created_at >= (NOW() - INTERVAL 1 DAY)
    ");
    $q->bind_param("i", $user_id);
    $q->execute();
    list($count, $sum) = $q->get_result()->fetch_row();
    $q->close();

    // Average daily expense (last 30 days)
    $q2 = $conn->prepare("
        SELECT IFNULL(SUM(amount) / 30, 0)
        FROM transactions
        WHERE user_id = ?
        AND type = 'expense'
        AND date >= (CURDATE() - INTERVAL 30 DAY)
    ");
    $q2->bind_param("i", $user_id);
    $q2->execute();
    $avg = $q2->get_result()->fetch_row()[0];
    $q2->close();

    $flag = false;

    // Flag if: 4+ transactions in 24h AND spending is 2x daily average
    if ($count >= 4 && $sum > ($avg * 2)) {
        $flag = true;
    }

    return [
        "flag" => $flag,
        "todayCount" => (int)$count,
        "todayAmount" => (float)$sum
    ];
}
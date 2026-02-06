<?php
// finance_context.php - Fetch Financial Context
// Gets the user's current financial status for the chatbot

function fetchFinancialContext($conn, $user_id) {
    $monthStart = date('Y-m-01');
    $monthEnd = date('Y-m-t');

    // Total income (this month)
    $q1 = $conn->prepare("
        SELECT IFNULL(SUM(amount), 0)
        FROM transactions
        WHERE user_id = ? AND type = 'income'
        AND date BETWEEN ? AND ?
    ");
    $q1->bind_param("iss", $user_id, $monthStart, $monthEnd);
    $q1->execute();
    $income = $q1->get_result()->fetch_row()[0];
    $q1->close();

    // Total expenses (this month)
    $q2 = $conn->prepare("
        SELECT IFNULL(SUM(amount), 0)
        FROM transactions
        WHERE user_id = ? AND type = 'expense'
        AND date BETWEEN ? AND ?
    ");
    $q2->bind_param("iss", $user_id, $monthStart, $monthEnd);
    $q2->execute();
    $expense = $q2->get_result()->fetch_row()[0];
    $q2->close();

    // Total savings balance (all time)
    $q3 = $conn->prepare("
        SELECT IFNULL(
            SUM(CASE 
                WHEN type = 'savings' THEN amount
                WHEN type = 'withdraw_savings' THEN -amount
                ELSE 0 
            END), 0)
        FROM transactions
        WHERE user_id = ?
    ");
    $q3->bind_param("i", $user_id);
    $q3->execute();
    $savings = $q3->get_result()->fetch_row()[0];
    $q3->close();

    return [
        "income" => (float)$income,
        "expense" => (float)$expense,
        "savings" => (float)$savings,
        "remaining" => (float)$income - (float)$expense
    ];
}
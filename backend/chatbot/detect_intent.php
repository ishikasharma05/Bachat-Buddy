<?php
// detect_intent.php - Enhanced Intent Detection with Overview Support
// Analyzes user messages to understand what they're asking about

function detectIntent($message) {
    $message = strtolower(trim($message));
    
    // Greeting patterns
    $greetings = ['hi', 'hello', 'hey', 'hola', 'namaste', 'good morning', 'good evening', 'whats up', "what's up"];
    foreach ($greetings as $greeting) {
        if (strpos($message, $greeting) !== false) {
            return 'greeting';
        }
    }
    
    // Overview patterns - must check BEFORE specific patterns
    $overviewKeywords = [
        'overview', 'everything', 'all details', 'complete', 'summary', 'full picture',
        'show me everything', 'tell me everything', 'financial status', 'status',
        'how am i doing', 'how am i', 'my finances', 'financial overview'
    ];
    foreach ($overviewKeywords as $keyword) {
        if (strpos($message, $keyword) !== false) {
            return 'overview';
        }
    }
    
    // Savings status patterns
    $savingsKeywords = ['saving', 'savings', 'saved', 'how much saved', 'savings balance', 'total savings'];
    foreach ($savingsKeywords as $keyword) {
        if (strpos($message, $keyword) !== false) {
            return 'savings_status';
        }
    }
    
    // Expense summary patterns
    $expenseKeywords = ['expense', 'spent', 'spending', 'how much spent', 'total expense', 'expenditure'];
    foreach ($expenseKeywords as $keyword) {
        if (strpos($message, $keyword) !== false) {
            return 'expense_summary';
        }
    }
    
    // Income check patterns
    $incomeKeywords = ['income', 'earned', 'earning', 'salary', 'total income'];
    foreach ($incomeKeywords as $keyword) {
        if (strpos($message, $keyword) !== false) {
            return 'income_status';
        }
    }
    
    // Purchase check patterns
    $purchaseKeywords = ['buy', 'purchase', 'should i buy', 'want to buy', 'thinking of buying', 'afford', 'get a'];
    foreach ($purchaseKeywords as $keyword) {
        if (strpos($message, $keyword) !== false) {
            return 'purchase_check';
        }
    }
    
    // Budget advice patterns
    $budgetKeywords = ['budget', 'plan', 'advice', 'help me save', 'tip', 'suggestion'];
    foreach ($budgetKeywords as $keyword) {
        if (strpos($message, $keyword) !== false) {
            return 'budget_advice';
        }
    }
    
    // Thank you patterns
    $thanksKeywords = ['thank', 'thanks', 'appreciated', 'helpful'];
    foreach ($thanksKeywords as $keyword) {
        if (strpos($message, $keyword) !== false) {
            return 'thanks';
        }
    }
    
    // Default
    return 'general';
}
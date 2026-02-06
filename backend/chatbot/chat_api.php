<?php
// chat_api.php - Enhanced Chatbot API with Conversation Context
session_start();
require_once '../../config/db.php';
require_once 'detect_intent.php';
require_once 'finance_context.php';
require_once 'impulse_detector.php';
require_once 'response_builder.php';

header('Content-Type: application/json');

// Check if user is logged in
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode([
        "reply" => "Hey! 🙂 Please log in first so I can help you with your finances properly.",
        "followup" => [],
        "soft_popup" => null
    ]);
    exit;
}

// Get user input
$input = json_decode(file_get_contents("php://input"), true);
$message = trim($input['message'] ?? '');

if ($message == '') {
    echo json_encode([
        "reply" => "I'm listening! 😊 What would you like to know?",
        "followup" => [],
        "soft_popup" => null
    ]);
    exit;
}

// Initialize conversation context in session if not exists
if (!isset($_SESSION['chat_context'])) {
    $_SESSION['chat_context'] = [
        'state' => 'idle',
        'data' => []
    ];
}

$chatContext = &$_SESSION['chat_context'];

// Fetch user's financial context
$context = fetchFinancialContext($conn, $user_id);

// Check for impulse spending
$impulse = detectImpulse($conn, $user_id);

// Process message based on current conversation state
$response = processConversation($message, $chatContext, $context, $impulse);

// Return JSON response
echo json_encode($response);

/**
 * Main conversation processing function
 */
function processConversation($message, &$chatContext, $financialContext, $impulse) {
    $messageLower = strtolower(trim($message));
    
    // Check if user is in middle of a conversation flow
    switch ($chatContext['state']) {
        case 'awaiting_purchase_price':
            return handlePurchasePrice($message, $chatContext, $financialContext);
        
        case 'awaiting_purchase_necessity':
            return handlePurchaseNecessity($message, $chatContext, $financialContext);
        
        case 'awaiting_purchase_impact':
            return handlePurchaseImpact($message, $chatContext, $financialContext);
        
        default:
            // New conversation - detect intent
            return handleNewMessage($message, $chatContext, $financialContext, $impulse);
    }
}

/**
 * Handle new messages (not part of ongoing conversation)
 */
function handleNewMessage($message, &$chatContext, $context, $impulse) {
    $intent = detectIntent($message);
    $messageLower = strtolower(trim($message));
    
    // Check for affirmative responses (yes/yeah/ok/sure)
    if (preg_match('/\b(yes|yeah|yep|yup|sure|ok|okay|alright)\b/i', $messageLower)) {
        // User said yes but we're not in a conversation - provide menu
        return [
            "reply" => "I'm here to help you with your finances! 😊 You can ask me about:\n\n💰 Your savings balance\n💸 How much you've spent\n🛒 Whether you should buy something\n💡 Budget tips and advice\n\nWhat would you like to know?",
            "followup" => [],
            "soft_popup" => null
        ];
    }
    
    $response = buildResponse($intent, $message, $context, $impulse);
    
    // If this is a purchase check, set conversation state
    if ($intent === 'purchase_check') {
        $chatContext['state'] = 'awaiting_purchase_price';
        $chatContext['data'] = [];
    }
    
    return $response;
}

/**
 * Handle purchase price response
 */
function handlePurchasePrice($message, &$chatContext, $context) {
    $messageLower = strtolower(trim($message));
    
    // Extract price from message
    $price = extractPrice($message);
    
    if ($price === null) {
        // Couldn't find a price, ask again
        return [
            "reply" => "I didn't catch the price. 😅 Could you tell me approximately how much it costs? (Just the number is fine, like 50000 or 5000)",
            "followup" => [],
            "soft_popup" => null
        ];
    }
    
    // Store price and move to next question
    $chatContext['data']['price'] = $price;
    $chatContext['state'] = 'awaiting_purchase_necessity';
    
    return [
        "reply" => "Got it! ₹" . number_format($price, 0) . " 💰\n\nNow, is this something you really need right now, or is it more of a 'nice to have'? (No judgment, just helping you think clearly! 😊)",
        "followup" => [],
        "soft_popup" => null
    ];
}

/**
 * Handle purchase necessity response
 */
function handlePurchaseNecessity($message, &$chatContext, $context) {
    $messageLower = strtolower(trim($message));
    
    // Determine if it's a need or want
    $isNeed = preg_match('/\b(need|necessary|must|important|essential|required)\b/i', $messageLower);
    $isWant = preg_match('/\b(want|like|nice|wish|prefer)\b/i', $messageLower);
    
    if (!$isNeed && !$isWant) {
        // Unclear response
        return [
            "reply" => "Hmm, I'm not quite sure. 🤔 Could you tell me - do you absolutely NEED this item now, or is it something you WANT but could wait for?",
            "followup" => [],
            "soft_popup" => null
        ];
    }
    
    $chatContext['data']['necessity'] = $isNeed ? 'need' : 'want';
    $chatContext['state'] = 'awaiting_purchase_impact';
    
    return [
        "reply" => "Thanks for sharing! 😊\n\nOne more thing - will this purchase affect your savings goals or monthly budget?",
        "followup" => [],
        "soft_popup" => null
    ];
}

/**
 * Handle purchase impact response and give final advice
 */
function handlePurchaseImpact($message, &$chatContext, $context) {
    $messageLower = strtolower(trim($message));
    $price = $chatContext['data']['price'] ?? 0;
    $necessity = $chatContext['data']['necessity'] ?? 'want';
    
    // Calculate financial impact
    $remaining = $context['remaining'];
    $savings = $context['savings'];
    $percentOfRemaining = $remaining > 0 ? ($price / $remaining) * 100 : 0;
    $percentOfSavings = $savings > 0 ? ($price / $savings) * 100 : 0;
    
    // Generate personalized advice
    $advice = generatePurchaseAdvice($price, $necessity, $remaining, $savings, $percentOfRemaining, $percentOfSavings);
    
    // Reset conversation state
    $chatContext['state'] = 'idle';
    $chatContext['data'] = [];
    
    return [
        "reply" => $advice,
        "followup" => [],
        "soft_popup" => null
    ];
}

/**
 * Extract price from user message
 */
function extractPrice($message) {
    // Remove commas and currency symbols
    $cleaned = preg_replace('/[,₹\s]/', '', $message);
    
    // Look for numbers
    if (preg_match('/(\d+(?:\.\d+)?)/', $cleaned, $matches)) {
        return floatval($matches[1]);
    }
    
    return null;
}

/**
 * Generate personalized purchase advice
 */
function generatePurchaseAdvice($price, $necessity, $remaining, $savings, $percentOfRemaining, $percentOfSavings) {
    $priceFormatted = "₹" . number_format($price, 0);
    $remainingFormatted = "₹" . number_format($remaining, 0);
    
    $advice = "Alright, let me help you think this through! 🤔\n\n";
    $advice .= "📊 The Numbers:\n";
    $advice .= "• Purchase: {$priceFormatted}\n";
    $advice .= "• You have left this month: {$remainingFormatted}\n";
    
    if ($percentOfRemaining > 0) {
        $advice .= "• This is " . round($percentOfRemaining, 1) . "% of your remaining budget\n\n";
    }
    
    // Decision logic
    if ($necessity === 'need') {
        if ($percentOfRemaining <= 30) {
            $advice .= "✅ My Recommendation: Go for it!\n\n";
            $advice .= "Since this is something you need and it's only " . round($percentOfRemaining, 1) . "% of your remaining budget, it seems reasonable. Just make sure you have enough for other essentials this month! 😊";
        } elseif ($percentOfRemaining <= 60) {
            $advice .= "⚠️ My Recommendation: Proceed with caution\n\n";
            $advice .= "This will take " . round($percentOfRemaining, 1) . "% of your remaining budget. Since it's a need, you might have to buy it, but consider:\n";
            $advice .= "• Can you find it cheaper elsewhere?\n";
            $advice .= "• Do you have enough for other monthly expenses?\n";
            $advice .= "• Could you wait for a sale?";
        } else {
            $advice .= "🛑 My Recommendation: Think carefully!\n\n";
            $advice .= "This will take most of your remaining budget (" . round($percentOfRemaining, 1) . "%). Even though it's a need, consider:\n";
            $advice .= "• Can you spread the payment?\n";
            $advice .= "• Is there a cheaper alternative?\n";
            $advice .= "• Will you have enough for emergencies?";
        }
    } else { // want
        if ($percentOfRemaining <= 15) {
            $advice .= "✅ My Recommendation: Treat yourself!\n\n";
            $advice .= "This is only " . round($percentOfRemaining, 1) . "% of your remaining budget, and it's okay to enjoy life sometimes! Just make sure all your needs are covered first. 😊";
        } elseif ($percentOfRemaining <= 40) {
            $advice .= "⚠️ My Recommendation: Maybe wait a bit?\n\n";
            $advice .= "This will take " . round($percentOfRemaining, 1) . "% of your remaining budget. Since it's a want, consider:\n";
            $advice .= "• Could you save up for it next month?\n";
            $advice .= "• Will you still want it in 2-3 days?\n";
            $advice .= "• Are there more important priorities?";
        } else {
            $advice .= "🛑 My Recommendation: Better to skip for now\n\n";
            $advice .= "This is " . round($percentOfRemaining, 1) . "% of your remaining budget for something you want (not need). My friendly advice:\n";
            $advice .= "• Wait until next month\n";
            $advice .= "• Start saving for it gradually\n";
            $advice .= "• Future-you will thank present-you! 💪";
        }
    }
    
    $advice .= "\n\nRemember, I'm here to help you make smart choices, not to stop you from living! 💛";
    
    return $advice;
}
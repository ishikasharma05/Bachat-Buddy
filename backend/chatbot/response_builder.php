<?php
// response_builder.php - Enhanced Response Builder with Financial Overview
// Creates friendly, helpful responses based on user intent

function buildResponse($intent, $msg, $ctx, $impulse) {
    $reply = "";
    $followup = [];
    
    switch($intent) {
        case 'greeting':
            $greetings = [
                "Hey there! 😄 I'm Bachat Buddy, your friendly money helper. What's on your mind today?",
                "Hello! 🙂 I'm here to help you with your finances. What would you like to know?",
                "Hi! 💛 Ready to talk about your money goals? I'm all ears!"
            ];
            $reply = $greetings[array_rand($greetings)];
            break;
            
        case 'savings_status':
            $savings = number_format($ctx['savings'], 2);
            if ($ctx['savings'] > 0) {
                $reply = "Great news! 🎉 You currently have ₹{$savings} in your savings account. ";
                $reply .= "That's awesome! Keep it up! 💪";
            } else {
                $reply = "I see you haven't started saving yet, but that's totally okay! ";
                $reply .= "Every journey starts with a single step. Want to set a small savings goal? 😊";
            }
            break;
            
        case 'expense_summary':
            $expense = number_format($ctx['expense'], 2);
            $remaining = number_format($ctx['remaining'], 2);
            
            $reply = "This month, you've spent ₹{$expense}. ";
            
            if ($ctx['remaining'] > 0) {
                $reply .= "Good news! You still have ₹{$remaining} remaining from your income. 😊";
            } else {
                $reply .= "Looks like you've spent more than your income this month. ";
                $reply .= "Let's work together to get back on track! 💪";
            }
            break;
            
        case 'income_status':
            $income = number_format($ctx['income'], 2);
            $reply = "Your total income this month is ₹{$income}. ";
            
            if ($ctx['expense'] > 0) {
                $savingsRate = (($ctx['income'] - $ctx['expense']) / $ctx['income']) * 100;
                if ($savingsRate > 20) {
                    $reply .= "And you're doing amazing! You're saving over 20% of your income! 🌟";
                } elseif ($savingsRate > 0) {
                    $reply .= "You're saving some money, which is great! Try to push it above 20% if you can. 😊";
                }
            }
            break;
            
        case 'purchase_check':
            $reply = "Let's think about this purchase together! 🤔 I want to make sure it's right for you.";
            $followup[] = "How much does it cost approximately?";
            break;
            
        case 'budget_advice':
            $tips = [
                "💡 Try the 50-30-20 rule: 50% needs, 30% wants, 20% savings!",
                "💡 Track every expense for a week - you'll be surprised where your money goes!",
                "💡 Before buying something, wait 24 hours. If you still want it, then consider it!",
                "💡 Automate your savings - save before you spend!",
                "💡 Cut one small expense and redirect it to savings - it adds up! 🎯"
            ];
            $reply = "Here's a friendly tip for you: " . $tips[array_rand($tips)];
            break;
            
        case 'thanks':
            $responses = [
                "You're very welcome! 😊 I'm always here to help!",
                "Happy to help! That's what friends are for! 💛",
                "Anytime! We're in this together! 💪"
            ];
            $reply = $responses[array_rand($responses)];
            break;
            
        case 'overview':
            $reply = buildFinancialOverview($ctx);
            break;
            
        case 'general':
        default:
            $reply = "I'm here to help you with your finances! 😊 You can ask me about:\n\n";
            $reply .= "💰 Your savings balance\n";
            $reply .= "💸 How much you've spent\n";
            $reply .= "🛒 Whether you should buy something\n";
            $reply .= "💡 Budget tips and advice\n\n";
            $reply .= "What would you like to know?";
            break;
    }
    
    // Check for impulse spending warning
    $softPopup = null;
    if ($impulse['flag']) {
        $todayAmount = number_format($impulse['todayAmount'], 2);
        $softPopup = "Hey! 👀 Just noticed you've made {$impulse['todayCount']} purchases today (₹{$todayAmount}). ";
        $softPopup .= "That's more than usual. Everything okay? Want to review your spending? 🙂";
    }
    
    return [
        "reply" => $reply,
        "followup" => $followup,
        "soft_popup" => $softPopup,
        "context_used" => [
            "income" => $ctx['income'],
            "expense" => $ctx['expense'],
            "savings" => $ctx['savings']
        ]
    ];
}

/**
 * Build a comprehensive financial overview
 */
function buildFinancialOverview($ctx) {
    $income = number_format($ctx['income'], 2);
    $expense = number_format($ctx['expense'], 2);
    $savings = number_format($ctx['savings'], 2);
    $remaining = number_format($ctx['remaining'], 2);
    
    $overview = "📊 Here's your complete financial overview:\n\n";
    
    // Income section
    $overview .= "💵 INCOME (This Month)\n";
    $overview .= "₹{$income}\n\n";
    
    // Expense section
    $overview .= "💸 EXPENSES (This Month)\n";
    $overview .= "₹{$expense}\n\n";
    
    // Savings section
    $overview .= "🏦 TOTAL SAVINGS\n";
    $overview .= "₹{$savings}\n\n";
    
    // Remaining section
    $overview .= "💰 REMAINING THIS MONTH\n";
    $overview .= "₹{$remaining}\n\n";
    
    // Financial health check
    if ($ctx['income'] > 0) {
        $savingsRate = (($ctx['income'] - $ctx['expense']) / $ctx['income']) * 100;
        $overview .= "📈 FINANCIAL HEALTH\n";
        
        if ($savingsRate > 30) {
            $overview .= "🌟 Excellent! You're saving " . round($savingsRate, 1) . "% - keep it up!";
        } elseif ($savingsRate > 20) {
            $overview .= "✅ Great! You're saving " . round($savingsRate, 1) . "% - that's healthy!";
        } elseif ($savingsRate > 10) {
            $overview .= "👍 Good! You're saving " . round($savingsRate, 1) . "% - try to increase it!";
        } elseif ($savingsRate > 0) {
            $overview .= "⚠️ You're saving " . round($savingsRate, 1) . "% - aim for at least 20%!";
        } else {
            $overview .= "🛑 Warning: You're spending more than you earn. Time to review your budget!";
        }
    } else {
        $overview .= "💡 Start tracking your income to get personalized insights!";
    }
    
    return $overview;
}
<?php
// Start session and check authentication
require_once 'components/auth_check.php';
require_once 'config/db.php';

// Fetch all transactions directly in PHP
$user_id = $_SESSION['user_id'];
$transactions = [];
$error = null;

try {
    $stmt = $conn->prepare("
        SELECT id, type, amount, category, description, tags, date, created_at
        FROM transactions 
        WHERE user_id = ? 
        ORDER BY date DESC, created_at DESC
    ");
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
        
        $stmt->close();
    } else {
        $error = "Failed to prepare query";
    }
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bachat Buddy | Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="components/style.css">
    <style>
        /* ===================================================================
           GLOBAL UI DESIGN SYSTEM
           A comprehensive design system for modern fintech applications
           ================================================================ */

        /* ------------------------------------------------------------------
           COLOR PALETTE
           - Primary: Modern teal/cyan for CTAs and important elements
           - Secondary: Deep indigo for accents and secondary actions
           - Success: Green for positive actions and income
           - Danger: Red for destructive actions and expenses
           - Neutral: Grayscale for text and backgrounds
           - Surfaces: Card and container backgrounds
           
           Why: Financial apps need trust and clarity. Teal conveys 
           professionalism and reliability. Clear semantic colors (green/red)
           help users instantly recognize income vs expenses.
           ---------------------------------------------------------------- */
        :root {
            /* Primary Colors */
            --color-primary-50: #e0f7fa;
            --color-primary-100: #b2ebf2;
            --color-primary-200: #80deea;
            --color-primary-300: #4dd0e1;
            --color-primary-400: #26c6da;
            --color-primary-500: #00bcd4;
            --color-primary-600: #00acc1;
            --color-primary-700: #0097a7;
            --color-primary-800: #00838f;
            --color-primary-900: #006064;

            /* Secondary Colors */
            --color-secondary-50: #e8eaf6;
            --color-secondary-100: #c5cae9;
            --color-secondary-200: #9fa8da;
            --color-secondary-300: #7986cb;
            --color-secondary-400: #5c6bc0;
            --color-secondary-500: #3f51b5;
            --color-secondary-600: #3949ab;
            --color-secondary-700: #303f9f;
            --color-secondary-800: #283593;
            --color-secondary-900: #1a237e;

            /* Success Colors */
            --color-success-50: #e8f5e9;
            --color-success-100: #c8e6c9;
            --color-success-200: #a5d6a7;
            --color-success-300: #81c784;
            --color-success-400: #66bb6a;
            --color-success-500: #4caf50;
            --color-success-600: #43a047;
            --color-success-700: #388e3c;
            --color-success-800: #2e7d32;
            --color-success-900: #1b5e20;

            /* Danger Colors */
            --color-danger-50: #ffebee;
            --color-danger-100: #ffcdd2;
            --color-danger-200: #ef9a9a;
            --color-danger-300: #e57373;
            --color-danger-400: #ef5350;
            --color-danger-500: #f44336;
            --color-danger-600: #e53935;
            --color-danger-700: #d32f2f;
            --color-danger-800: #c62828;
            --color-danger-900: #b71c1c;

            /* Warning Colors */
            --color-warning-50: #fff8e1;
            --color-warning-100: #ffecb3;
            --color-warning-200: #ffe082;
            --color-warning-300: #ffd54f;
            --color-warning-400: #ffca28;
            --color-warning-500: #ffc107;
            --color-warning-600: #ffb300;
            --color-warning-700: #ffa000;
            --color-warning-800: #ff8f00;
            --color-warning-900: #ff6f00;

            /* Info Colors */
            --color-info-50: #e3f2fd;
            --color-info-100: #bbdefb;
            --color-info-200: #90caf9;
            --color-info-300: #64b5f6;
            --color-info-400: #42a5f5;
            --color-info-500: #2196f3;
            --color-info-600: #1e88e5;
            --color-info-700: #1976d2;
            --color-info-800: #1565c0;
            --color-info-900: #0d47a1;

            /* Neutral Colors (Light Theme) */
            --color-neutral-50: #fafafa;
            --color-neutral-100: #f5f5f5;
            --color-neutral-200: #eeeeee;
            --color-neutral-300: #e0e0e0;
            --color-neutral-400: #bdbdbd;
            --color-neutral-500: #9e9e9e;
            --color-neutral-600: #757575;
            --color-neutral-700: #616161;
            --color-neutral-800: #424242;
            --color-neutral-900: #212121;

            /* Semantic Color Mappings */
            --bg-main: #f8fafb;
            --bg-card: #ffffff;
            --bg-elevated: #ffffff;
            --text-primary: #1a202c;
            --text-secondary: #4a5568;
            --text-muted: #718096;
            --border-light: #e2e8f0;
            --border-medium: #cbd5e0;
            --sidebar-bg: #ffffff;
            --header-bg: #ffffff;
            --input-bg: #f7fafc;
            --table-header: #f8fafc;
            --table-row-hover: #f7fafc;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Dark Theme Colors */
        [data-theme="dark"] {
            --bg-main: #0f172a;
            --bg-card: #1e293b;
            --bg-elevated: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-light: #334155;
            --border-medium: #475569;
            --sidebar-bg: #1e293b;
            --header-bg: #1e293b;
            --input-bg: #334155;
            --table-header: #0f172a;
            --table-row-hover: #334155;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.4);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.6), 0 10px 10px -5px rgba(0, 0, 0, 0.5);
        }

        /* ------------------------------------------------------------------
           TYPOGRAPHY SYSTEM
           - Font Family: Inter - a modern, highly readable sans-serif
           - Font Sizes: Modular scale for consistency
           - Line Heights: Optimized for readability
           
           Why: Inter is designed for UI and offers excellent legibility at 
           all sizes. The modular scale ensures visual hierarchy and 
           consistency across the application.
           ---------------------------------------------------------------- */
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            margin: 0;
            background-color: var(--bg-main);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
            font-size: 15px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Typography Scale */
        .text-display {
            font-size: 3rem;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .text-h1 {
            font-size: 2.25rem;
            line-height: 1.25;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .text-h2 {
            font-size: 1.875rem;
            line-height: 1.3;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .text-h3 {
            font-size: 1.5rem;
            line-height: 1.35;
            font-weight: 600;
        }

        .text-h4 {
            font-size: 1.25rem;
            line-height: 1.4;
            font-weight: 600;
        }

        .text-h5 {
            font-size: 1.125rem;
            line-height: 1.45;
            font-weight: 600;
        }

        .text-body-lg {
            font-size: 1.125rem;
            line-height: 1.6;
        }

        .text-body {
            font-size: 1rem;
            line-height: 1.6;
        }

        .text-body-sm {
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .text-caption {
            font-size: 0.75rem;
            line-height: 1.4;
            letter-spacing: 0.01em;
        }

        /* ------------------------------------------------------------------
           BUTTON SYSTEM
           - Clear visual hierarchy (primary, secondary, ghost, danger)
           - Consistent sizing and spacing
           - Smooth hover and active states
           
           Why: Buttons are primary interaction points. Clear visual weight
           guides users to important actions. Hover states provide feedback.
           ---------------------------------------------------------------- */
        .btn-modern {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9375rem;
            border: 2px solid transparent;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-modern:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.15);
        }

        /* Primary Button - Main CTAs */
        .btn-primary-modern {
            background: linear-gradient(135deg, var(--color-primary-500), var(--color-primary-600));
            color: white;
            border-color: var(--color-primary-500);
        }

        .btn-primary-modern:hover {
            background: linear-gradient(135deg, var(--color-primary-600), var(--color-primary-700));
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 188, 212, 0.3);
        }

        .btn-primary-modern:active {
            transform: translateY(0);
        }

        /* Secondary Button - Less important actions */
        .btn-secondary-modern {
            background: var(--bg-card);
            color: var(--text-primary);
            border-color: var(--border-medium);
        }

        .btn-secondary-modern:hover {
            background: var(--bg-elevated);
            border-color: var(--color-primary-500);
            color: var(--color-primary-700);
        }

        /* Danger Button - Destructive actions */
        .btn-danger-modern {
            background: linear-gradient(135deg, var(--color-danger-500), var(--color-danger-600));
            color: white;
            border-color: var(--color-danger-500);
        }

        .btn-danger-modern:hover {
            background: linear-gradient(135deg, var(--color-danger-600), var(--color-danger-700));
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(244, 67, 54, 0.3);
        }

        /* Ghost Button - Minimal footprint */
        .btn-ghost-modern {
            background: transparent;
            color: var(--text-secondary);
            border-color: transparent;
        }

        .btn-ghost-modern:hover {
            background: var(--table-row-hover);
            color: var(--text-primary);
        }

        /* Icon Button */
        .btn-icon-modern {
            padding: 0.5rem;
            border-radius: 8px;
            background: var(--input-bg);
            color: var(--text-secondary);
            border: 1px solid var(--border-light);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-icon-modern:hover {
            background: var(--bg-elevated);
            color: var(--color-primary-600);
            border-color: var(--color-primary-300);
        }

        /* ------------------------------------------------------------------
           INPUT FIELD SYSTEM
           - Clear focus states for accessibility
           - Error and success states
           - Consistent sizing and spacing
           
           Why: Forms are critical for data entry. Clear states reduce 
           errors and improve user confidence.
           ---------------------------------------------------------------- ------------------------------------------------------------------
        .input-modern {
            background-color: var(--input-bg);
            border: 2px solid var(--border-light);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            width: 100%;
        }

        .input-modern:focus {
            outline: none;
            background-color: var(--bg-card);
            border-color: var(--color-primary-500);
            box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.1);
        }

        .input-modern::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
        }

        .input-modern.error {
            border-color: var(--color-danger-500);
        }

        .input-modern.success {
            border-color: var(--color-success-500);
        }

        /* Input Group Styling */
        .input-group-modern {
            display: flex;
            align-items: stretch;
            gap: 0.5rem;
        }

        .input-group-modern .input-prefix {
            display: flex;
            align-items: center;
            padding: 0 1rem;
            background: var(--input-bg);
            border: 2px solid var(--border-light);
            border-radius: 10px 0 0 10px;
            color: var(--text-muted);
            border-right: none;
        }

        .input-group-modern .input-modern {
            border-radius: 0 10px 10px 0;
        }

        .input-group-modern.single-input .input-modern {
            border-radius: 10px;
        }

        /* ------------------------------------------------------------------
           CARD SYSTEM
           - Elevation through subtle shadows
           - Consistent border radius
           - Responsive spacing
           
           Why: Cards organize content into digestible chunks. Shadows 
           create depth hierarchy without cluttering the interface.
           ---------------------------------------------------------------- */
        .card-modern {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-light);
            padding: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .card-modern-hover:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .card-modern-elevated {
            box-shadow: var(--shadow-lg);
        }

        /* ------------------------------------------------------------------
           LAYOUT COMPONENTS
           ---------------------------------------------------------------- */
        .layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-light);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem 1rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            text-decoration: none;
            margin-bottom: 0.25rem;
        }

        .sidebar .nav-link:hover {
            background: linear-gradient(135deg, var(--color-primary-500), var(--color-primary-600));
            color: white;
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--color-primary-500), var(--color-primary-600));
            color: white;
            box-shadow: 0 4px 12px rgba(0, 188, 212, 0.25);
        }

        .sidebar .nav-link i {
            margin-right: 0.875rem;
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
        }

        .brand {
            font-weight: 800;
            font-size: 1.375rem;
            padding: 0.75rem 1rem;
            margin-bottom: 2rem;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }

        .brand i {
            color: var(--color-success-500);
            font-size: 1.75rem;
        }

        /* Header Styling */
        .header {
            background-color: var(--header-bg);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-light);
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .search-box {
            background: var(--input-bg);
            border: 2px solid var(--border-light);
            border-radius: 12px;
            padding: 0.625rem 1.25rem;
            width: 350px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
        }

        .search-box:focus-within {
            border-color: var(--color-primary-500);
            background: var(--bg-card);
            box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.1);
        }

        .search-box i {
            color: var(--text-muted);
            font-size: 1.125rem;
        }

        .search-box input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            color: var(--text-primary);
            font-size: 0.9375rem;
        }

        .notification,
        #theme-toggle {
            background: var(--input-bg);
            padding: 0.625rem;
            border-radius: 12px;
            border: 2px solid var(--border-light);
            cursor: pointer;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            width: 42px;
            height: 42px;
        }

        .notification:hover,
        #theme-toggle:hover {
            background: var(--bg-elevated);
            border-color: var(--color-primary-400);
            color: var(--color-primary-600);
            transform: scale(1.05);
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--input-bg);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            border: 2px solid var(--border-light);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .profile-info:hover {
            background: var(--bg-elevated);
            border-color: var(--color-primary-400);
        }

        .profile-info img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-text {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.9375rem;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        .main-body {
            padding: 2rem;
            overflow-y: auto;
            background-color: var(--bg-main);
            transition: all 0.3s ease;
        }

        /* ------------------------------------------------------------------
           TRANSACTION PAGE SPECIFIC STYLES
           ---------------------------------------------------------------- */

        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            letter-spacing: -0.01em;
        }

        .page-subtitle {
            color: var(--text-muted);
            font-size: 1rem;
            margin: 0;
        }

        /* Filter Section */
        .filter-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-sm);
        }

        .filter-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: stretch;
        }

        .filter-item {
            flex: 1;
            min-width: 200px;
        }

        .filter-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Transaction Cards (for mobile and card view) */
        .transaction-card-item {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .transaction-card-item:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
            border-color: var(--color-primary-300);
        }

        .transaction-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .transaction-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .transaction-icon.income {
            background: linear-gradient(135deg, var(--color-success-100), var(--color-success-200));
            color: var(--color-success-700);
        }

        .transaction-icon.expense {
            background: linear-gradient(135deg, var(--color-danger-100), var(--color-danger-200));
            color: var(--color-danger-700);
        }

        .transaction-icon.savings {
            background: linear-gradient(135deg, var(--color-info-100), var(--color-info-200));
            color: var(--color-info-700);
        }

        .transaction-icon.withdraw_savings {
            background: linear-gradient(135deg, var(--color-warning-100), var(--color-warning-200));
            color: var(--color-warning-700);
        }

        .transaction-details {
            flex: 1;
        }

        .transaction-description {
            font-weight: 600;
            font-size: 1rem;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .transaction-meta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }

        .transaction-date {
            font-size: 0.8125rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .transaction-amount {
            font-size: 1.25rem;
            font-weight: 700;
            text-align: right;
        }

        .transaction-amount.income {
            color: var(--color-success-600);
        }

        .transaction-amount.expense {
            color: var(--color-danger-600);
        }

        .transaction-amount.savings {
            color: var(--color-info-600);
        }

        .transaction-amount.withdraw_savings {
            color: var(--color-warning-600);
        }

        /* Category Badge System */
        .badge-modern {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 600;
            border: 1.5px solid;
        }

        /* Category-specific badges */
        .badge-food {
            background: var(--color-danger-50);
            color: var(--color-danger-700);
            border-color: var(--color-danger-200);
        }

        .badge-transport {
            background: var(--color-info-50);
            color: var(--color-info-700);
            border-color: var(--color-info-200);
        }

        .badge-entertainment {
            background: var(--color-secondary-50);
            color: var(--color-secondary-700);
            border-color: var(--color-secondary-200);
        }

        .badge-shopping {
            background: var(--color-warning-50);
            color: var(--color-warning-700);
            border-color: var(--color-warning-200);
        }

        .badge-health {
            background: var(--color-success-50);
            color: var(--color-success-700);
            border-color: var(--color-success-200);
        }

        .badge-salary {
            background: var(--color-success-50);
            color: var(--color-success-700);
            border-color: var(--color-success-200);
        }

        .badge-investment {
            background: var(--color-primary-50);
            color: var(--color-primary-700);
            border-color: var(--color-primary-200);
        }

        .badge-default {
            background: var(--color-neutral-100);
            color: var(--color-neutral-700);
            border-color: var(--color-neutral-300);
        }

        /* Type Badges */
        .badge-type {
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.8125rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-type.income {
            background: linear-gradient(135deg, var(--color-success-500), var(--color-success-600));
            color: white;
        }

        .badge-type.expense {
            background: linear-gradient(135deg, var(--color-danger-500), var(--color-danger-600));
            color: white;
        }

        .badge-type.savings {
            background: linear-gradient(135deg, var(--color-info-500), var(--color-info-600));
            color: white;
        }

        .badge-type.withdraw_savings {
            background: linear-gradient(135deg, var(--color-warning-500), var(--color-warning-600));
            color: white;
        }

        /* Modern Table Styling */
        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead th {
            background: var(--table-header);
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 1.25rem;
            border-bottom: 2px solid var(--border-light);
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table-modern tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid var(--border-light);
        }

        .table-modern tbody tr:hover {
            background: var(--table-row-hover);
            transform: scale(1.005);
        }

        .table-modern tbody td {
            padding: 1.25rem;
            color: var(--text-primary);
            vertical-align: middle;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state-icon {
            font-size: 4rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }

        .empty-state-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .empty-state-text {
            color: var(--text-muted);
            font-size: 1rem;
        }

        /* Enhanced Pagination */
        .pagination-modern {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem;
            background: var(--bg-card);
            border-radius: 16px;
            margin-top: 1.5rem;
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-sm);
        }

        .pagination-info {
            color: var(--text-secondary);
            font-size: 0.9375rem;
            font-weight: 500;
        }

        .pagination-info strong {
            color: var(--text-primary);
            font-weight: 700;
        }

        .pagination-controls {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .pagination-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: var(--input-bg);
            color: var(--text-primary);
            border: 2px solid var(--border-light);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
            font-size: 0.9375rem;
        }

        .pagination-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--color-primary-500), var(--color-primary-600));
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 188, 212, 0.3);
            border-color: transparent;
        }

        .pagination-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .page-indicator {
            padding: 0.625rem 1.5rem;
            background: linear-gradient(135deg, var(--color-primary-500), var(--color-primary-600));
            color: white;
            border-radius: 10px;
            font-weight: 700;
            min-width: 140px;
            text-align: center;
            font-size: 0.9375rem;
            box-shadow: 0 4px 12px rgba(0, 188, 212, 0.25);
        }

        /* Mobile Sidebar */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: -300px;
            width: 260px;
            height: 100vh;
            background-color: var(--sidebar-bg);
            z-index: 1050;
            transition: left 0.3s ease;
            overflow-y: auto;
            box-shadow: var(--shadow-xl);
        }

        .mobile-sidebar.active {
            left: 0;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(4px);
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-4px);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }

        .stat-icon.total {
            background: linear-gradient(135deg, var(--color-primary-100), var(--color-primary-200));
            color: var(--color-primary-700);
        }

        .stat-icon.income {
            background: linear-gradient(135deg, var(--color-success-100), var(--color-success-200));
            color: var(--color-success-700);
        }

        .stat-icon.expense {
            background: linear-gradient(135deg, var(--color-danger-100), var(--color-danger-200));
            color: var(--color-danger-700);
        }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            font-size: 0.8125rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.01em;
        }

        /* Dropdown Menu */
        .dropdown-menu-modern {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            box-shadow: var(--shadow-xl);
            padding: 0.5rem;
            min-width: 180px;
        }

        .dropdown-item-modern {
            padding: 0.625rem 1rem;
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 0.9375rem;
            font-weight: 500;
            transition: all 0.2s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dropdown-item-modern:hover {
            background: var(--table-row-hover);
            color: var(--color-primary-600);
        }

        .dropdown-item-modern.danger:hover {
            background: var(--color-danger-50);
            color: var(--color-danger-600);
        }

        .dropdown-divider-modern {
            height: 1px;
            background: var(--border-light);
            margin: 0.5rem 0;
        }

        /* Month Filter Special Styling */
        .month-filter-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--color-primary-50);
            color: var(--color-primary-700);
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            border: 2px solid var(--color-primary-200);
        }

        /* Responsive Design */
        @media (max-width: 991px) {
            .sidebar.d-lg-block {
                display: none !important;
            }

            .main-content {
                flex: 1;
                width: 100%;
            }

            .main-body {
                padding: 1.5rem 1rem;
            }

            .search-box {
                width: 100%;
                max-width: 300px;
            }
        }

        @media (max-width: 767px) {
            .filter-item {
                min-width: 100%;
            }

            .pagination-modern {
                flex-direction: column;
                gap: 1rem;
            }

            .pagination-controls {
                width: 100%;
                justify-content: center;
            }

            .pagination-btn {
                flex: 1;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .stat-card {
                padding: 1.25rem;
            }

            .stat-icon {
                width: 48px;
                height: 48px;
                font-size: 1.5rem;
            }

            .stat-value {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 575px) {
            /* Hide table, show cards */
            .table-modern {
                display: none;
            }

            .transaction-card-view {
                display: block;
            }

            .header {
                padding: 1rem;
            }

            .search-box {
                max-width: 200px;
            }

            .profile-text {
                display: none;
            }
        }

        @media (min-width: 576px) {
            .transaction-card-view {
                display: none;
            }
        }

        /* Accessibility Improvements */
        :focus-visible {
            outline: 3px solid var(--color-primary-500);
            outline-offset: 2px;
        }

        /* Loading State */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid var(--border-light);
            border-top-color: var(--color-primary-500);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-main);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-medium);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }
    </style>
</head>

<body>
    <div class="layout">
        <!-- Desktop Sidebar -->
        <div class="sidebar d-none d-lg-block">
            <div>
                <div class="brand">
                    <i class="bi bi-piggy-bank-fill"></i>
                    <span>Bachat Buddy</span>
                </div>
                <ul class="nav flex-column">
                    <li><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link active" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>

        <!-- Mobile Sidebar -->
        <div id="mobileSidebar" class="mobile-sidebar d-lg-none">
            <div class="p-4">
                <div class="brand d-flex align-items-center justify-content-between mb-4">
                    <span><i class="bi bi-piggy-bank-fill text-success"></i> Bachat Buddy</span>
                    <button onclick="toggleMenu()" class="btn-close border-0 bg-transparent text-muted fs-4" aria-label="Close menu">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <ul class="nav flex-column">
                    <li><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link active" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>
        <div id="sidebarOverlay" class="sidebar-overlay d-lg-none" onclick="toggleMenu()"></div>

        <!-- Main Content -->
        <div class="main-content">
            <?php include 'components/header.php'; ?>
            
            <div class="main-body">
                <div class="container-fluid">
                    <!-- Page Header -->
                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="bi bi-arrow-left-right me-2" style="color: var(--color-primary-500);"></i>
                            Transactions
                        </h1>
                        <p class="page-subtitle">Track and manage all your financial transactions in one place</p>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon total">
                                <i class="bi bi-list-ul"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Transactions</div>
                                <div class="stat-value" id="totalTransactionsStat">0</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon income">
                                <i class="bi bi-arrow-down-circle"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Income</div>
                                <div class="stat-value" style="color: var(--color-success-600);" id="totalIncomeStat">$0.00</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon expense">
                                <i class="bi bi-arrow-up-circle"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Expenses</div>
                                <div class="stat-value" style="color: var(--color-danger-600);" id="totalExpensesStat">$0.00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="filter-card">
                        <div class="filter-group">
                            <div class="filter-item">
                                <label class="filter-label">
                                    <i class="bi bi-search me-1"></i> Search
                                </label>
                                <input type="search" class="input-modern" id="searchInput" placeholder="Search by description, category, or tags..." />
                            </div>
                            <div class="filter-item">
                                <label class="filter-label">
                                    <i class="bi bi-funnel me-1"></i> Type
                                </label>
                                <select class="input-modern" id="typeFilter">
                                    <option value="all">All Types</option>
                                    <option value="income">Income Only</option>
                                    <option value="expense">Expenses Only</option>
                                    <option value="savings">Savings Only</option>
                                    <option value="withdraw_savings">Withdraw Savings</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label class="filter-label">
                                    <i class="bi bi-calendar-month me-1"></i> Month
                                </label>
                                <select class="input-modern" id="monthFilter">
                                    <option value="all">All Months</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label class="filter-label">
                                    <i class="bi bi-sort-down me-1"></i> Sort By
                                </label>
                                <select class="input-modern" id="sortFilter">
                                    <option value="date-desc">Newest First</option>
                                    <option value="date-asc">Oldest First</option>
                                    <option value="amount-desc">Amount: High to Low</option>
                                    <option value="amount-asc">Amount: Low to High</option>
                                </select>
                            </div>
                            <div class="filter-item" style="max-width: 180px;">
                                <label class="filter-label">
                                    <i class="bi bi-list-ol me-1"></i> Per Page
                                </label>
                                <select class="input-modern" id="itemsPerPageSelect">
                                    <option value="5">5 items</option>
                                    <option value="10" selected>10 items</option>
                                    <option value="15">15 items</option>
                                    <option value="20">20 items</option>
                                    <option value="50">50 items</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Table (Desktop) -->
                    <div class="card-modern">
                        <div class="table-responsive">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th style="width: 120px;">Date</th>
                                        <th>Description</th>
                                        <th style="width: 140px;">Category</th>
                                        <th style="width: 120px;">Type</th>
                                        <th style="width: 140px; text-align: right;">Amount</th>
                                        <th style="width: 80px; text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionTableBody">
                                    <?php if ($error): ?>
                                        <tr>
                                            <td colspan="6">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon">
                                                        <i class="bi bi-exclamation-triangle"></i>
                                                    </div>
                                                    <div class="empty-state-title">Error Loading Transactions</div>
                                                    <div class="empty-state-text"><?php echo htmlspecialchars($error); ?></div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php elseif (empty($transactions)): ?>
                                        <tr>
                                            <td colspan="6">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon">
                                                        <i class="bi bi-inbox"></i>
                                                    </div>
                                                    <div class="empty-state-title">No Transactions Yet</div>
                                                    <div class="empty-state-text">Start tracking your finances by adding your first transaction!</div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Transaction Cards (Mobile) -->
                    <div class="transaction-card-view" id="transactionCardView">
                        <!-- Cards will be inserted here by JavaScript -->
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-modern">
                        <div class="pagination-info">
                            Showing <strong id="showingStart">0</strong> to <strong id="showingEnd">0</strong> of <strong id="totalItems">0</strong> transactions
                        </div>
                        <div class="pagination-controls">
                            <button class="pagination-btn" id="prevBtn" onclick="previousPage()" aria-label="Previous page">
                                <i class="bi bi-chevron-left"></i>
                                <span class="d-none d-sm-inline">Previous</span>
                            </button>
                            <div class="page-indicator">
                                Page <span id="currentPageDisplay">1</span> / <span id="totalPagesDisplay">1</span>
                            </div>
                            <button class="pagination-btn" id="nextBtn" onclick="nextPage()" aria-label="Next page">
                                <span class="d-none d-sm-inline">Next</span>
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                    <?php include 'components/footer.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Chatbot Integration -->
    <button id="bbChatToggle" class="btn btn-primary bb-chat-btn" aria-label="Open Bachat Buddy Chat">
        💬
    </button>

    <div id="bbChatBox" class="card bb-chat-box d-none">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>💛 Bachat Buddy</span>
            <button class="btn btn-sm" onclick="toggleChat()" aria-label="Close chat">✖</button>
        </div>

        <div class="card-body bb-chat-body" id="bbChatBody">
            <div class="bb-bot-msg">
                <strong>Buddy:</strong> Hey there! 😄 I'm Bachat Buddy, your friendly money helper. Ask me about your spending, savings, or if you should make a purchase!
            </div>
        </div>

        <div class="card-footer p-2">
            <div class="input-group">
                <input type="text" id="bbChatInput" class="form-control" placeholder="Type your message..." aria-label="Chat message">
                <button class="btn btn-success" onclick="sendMessage()">Send</button>
            </div>
        </div>
    </div>

    <script src="components/js/chatbot.js"></script>

    <!-- Transactions Data -->
    <script>
        const transactions = <?php echo json_encode($transactions); ?>;
        console.log('Loaded transactions:', transactions.length);
    </script>

    <!-- Main Application Script -->
    <script>
        // ===================================================================
        // GLOBAL STATE MANAGEMENT
        // ===================================================================
        let itemsPerPage = 10;
        let currentPage = 1;
        let filteredTransactions = [];

        // ===================================================================
        // UTILITY FUNCTIONS
        // ===================================================================

        /**
         * Get category badge class based on category name
         * Why: Visual consistency helps users quickly identify transaction types
         */
        function getCategoryBadgeClass(category) {
            if (!category) return 'badge-default';
            
            const categoryLower = category.toLowerCase();
            const categoryMap = {
                'food': 'badge-food',
                'dining': 'badge-food',
                'groceries': 'badge-food',
                'transport': 'badge-transport',
                'travel': 'badge-transport',
                'commute': 'badge-transport',
                'entertainment': 'badge-entertainment',
                'movies': 'badge-entertainment',
                'games': 'badge-entertainment',
                'shopping': 'badge-shopping',
                'clothes': 'badge-shopping',
                'health': 'badge-health',
                'medical': 'badge-health',
                'fitness': 'badge-health',
                'salary': 'badge-salary',
                'income': 'badge-salary',
                'freelance': 'badge-salary',
                'investment': 'badge-investment',
                'stocks': 'badge-investment',
                'crypto': 'badge-investment'
            };

            for (const [key, value] of Object.entries(categoryMap)) {
                if (categoryLower.includes(key)) {
                    return value;
                }
            }

            return 'badge-default';
        }

        /**
         * Get transaction icon based on type
         */
        function getTransactionIcon(type) {
            const iconMap = {
                'income': 'bi-arrow-down-circle-fill',
                'expense': 'bi-arrow-up-circle-fill',
                'savings': 'bi-piggy-bank-fill',
                'withdraw_savings': 'bi-cash-coin'
            };
            return iconMap[type] || 'bi-circle-fill';
        }

        /**
         * Format currency
         */
        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 2
            }).format(Math.abs(amount));
        }

        /**
         * Format date
         */
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        /**
         * Get readable type name
         */
        function getTypeName(type) {
            return type.replace('_', ' ').split(' ').map(word => 
                word.charAt(0).toUpperCase() + word.slice(1)
            ).join(' ');
        }

        // ===================================================================
        // STATISTICS CALCULATION
        // ===================================================================
        function updateStatistics() {
            const totalTransactions = filteredTransactions.length;
            const totalIncome = filteredTransactions
                .filter(tx => tx.type === 'income')
                .reduce((sum, tx) => sum + parseFloat(tx.amount), 0);
            const totalExpenses = filteredTransactions
                .filter(tx => tx.type === 'expense')
                .reduce((sum, tx) => sum + Math.abs(parseFloat(tx.amount)), 0);

            document.getElementById('totalTransactionsStat').textContent = totalTransactions;
            document.getElementById('totalIncomeStat').textContent = formatCurrency(totalIncome);
            document.getElementById('totalExpensesStat').textContent = formatCurrency(totalExpenses);
        }

        // ===================================================================
        // MONTH FILTER POPULATION
        // ===================================================================
        function populateMonthFilter() {
            const monthFilter = document.getElementById('monthFilter');
            const months = new Set();

            transactions.forEach(tx => {
                const date = new Date(tx.date);
                const monthYear = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
                months.add(monthYear);
            });

            const sortedMonths = Array.from(months).sort().reverse();
            
            sortedMonths.forEach(monthYear => {
                const [year, month] = monthYear.split('-');
                const date = new Date(year, parseInt(month) - 1);
                const monthName = date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                
                const option = document.createElement('option');
                option.value = monthYear;
                option.textContent = monthName;
                monthFilter.appendChild(option);
            });
        }

        // ===================================================================
        // FILTER AND SORT LOGIC
        // ===================================================================
        function applyFilters() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const typeFilter = document.getElementById('typeFilter').value;
            const monthFilter = document.getElementById('monthFilter').value;
            const sortFilter = document.getElementById('sortFilter').value;

            // Filter transactions
            filteredTransactions = transactions.filter(tx => {
                // Type filter
                const matchesType = typeFilter === 'all' || tx.type.toLowerCase().replace(' ', '_') === typeFilter;
                
                // Search filter
                const matchesSearch = 
                    (tx.description && tx.description.toLowerCase().includes(searchTerm)) || 
                    (tx.category && tx.category.toLowerCase().includes(searchTerm)) ||
                    (tx.tags && tx.tags.toLowerCase().includes(searchTerm));
                
                // Month filter
                let matchesMonth = true;
                if (monthFilter !== 'all') {
                    const txDate = new Date(tx.date);
                    const txMonthYear = `${txDate.getFullYear()}-${String(txDate.getMonth() + 1).padStart(2, '0')}`;
                    matchesMonth = txMonthYear === monthFilter;
                }

                return matchesType && matchesSearch && matchesMonth;
            });

            // Sort transactions
            filteredTransactions.sort((a, b) => {
                if (sortFilter === 'date-desc') return new Date(b.date) - new Date(a.date);
                if (sortFilter === 'date-asc') return new Date(a.date) - new Date(b.date);
                if (sortFilter === 'amount-desc') return Math.abs(b.amount) - Math.abs(a.amount);
                if (sortFilter === 'amount-asc') return Math.abs(a.amount) - Math.abs(b.amount);
                return 0;
            });

            // Update statistics
            updateStatistics();

            // Reset to first page
            currentPage = 1;
            renderTransactions();
        }

        // ===================================================================
        // RENDER TRANSACTIONS
        // ===================================================================
        function renderTransactions() {
            const tbody = document.getElementById('transactionTableBody');
            const cardView = document.getElementById('transactionCardView');
            tbody.innerHTML = '';
            cardView.innerHTML = '';

            // Calculate pagination
            const totalPages = Math.ceil(filteredTransactions.length / itemsPerPage);
            const start = (currentPage - 1) * itemsPerPage;
            const end = Math.min(start + itemsPerPage, filteredTransactions.length);
            const paginated = filteredTransactions.slice(start, end);

            // Update pagination info
            document.getElementById('showingStart').textContent = filteredTransactions.length > 0 ? start + 1 : 0;
            document.getElementById('showingEnd').textContent = end;
            document.getElementById('totalItems').textContent = filteredTransactions.length;
            document.getElementById('currentPageDisplay').textContent = filteredTransactions.length > 0 ? currentPage : 0;
            document.getElementById('totalPagesDisplay').textContent = totalPages || 1;

            // Update button states
            document.getElementById('prevBtn').disabled = currentPage === 1;
            document.getElementById('nextBtn').disabled = currentPage >= totalPages;

            // Handle empty state
            if (paginated.length === 0) {
                const emptyMessage = filteredTransactions.length === 0 && transactions.length > 0
                    ? 'No transactions match your current filters'
                    : 'No transactions yet. Start by adding your first transaction!';
                
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="bi bi-${filteredTransactions.length === 0 && transactions.length > 0 ? 'search' : 'inbox'}"></i>
                                </div>
                                <div class="empty-state-title">${filteredTransactions.length === 0 && transactions.length > 0 ? 'No Results Found' : 'No Transactions Yet'}</div>
                                <div class="empty-state-text">${emptyMessage}</div>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            // Render each transaction
            paginated.forEach(tx => {
                // Table row (desktop)
                const tr = document.createElement('tr');
                const typeClass = tx.type.toLowerCase().replace(' ', '_');
                const categoryBadgeClass = getCategoryBadgeClass(tx.category);
                
                tr.innerHTML = `
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="bi bi-calendar3" style="color: var(--text-muted);"></i>
                            <span style="font-weight: 500;">${formatDate(tx.date)}</span>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-primary);">
                            ${tx.description || '<em style="color: var(--text-muted);">No description</em>'}
                        </div>
                        ${tx.tags ? `<div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.25rem;">
                            <i class="bi bi-tags" style="font-size: 0.75rem;"></i> ${tx.tags}
                        </div>` : ''}
                    </td>
                    <td>
                        <span class="badge-modern ${categoryBadgeClass}">
                            ${tx.category || 'Uncategorized'}
                        </span>
                    </td>
                    <td>
                        <span class="badge-type ${typeClass}">
                            ${getTypeName(tx.type)}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div class="transaction-amount ${typeClass}">
                            ${tx.type === 'expense' ? '-' : '+'}${formatCurrency(tx.amount)}
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <div class="dropdown">
                            <button class="btn-icon-modern" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Transaction actions">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-modern dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item-modern" href="edit-entry.php?id=${tx.id}">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>Edit</span>
                                    </a>
                                </li>
                                <li><div class="dropdown-divider-modern"></div></li>
                                <li>
                                    <button class="dropdown-item-modern danger" onclick="deleteTransaction(${tx.id}, '${(tx.description || 'this transaction').replace(/'/g, "\\'")}')">
                                        <i class="bi bi-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);

                // Card (mobile)
                const card = document.createElement('div');
                card.className = 'transaction-card-item';
                card.innerHTML = `
                    <div class="transaction-card-header">
                        <div style="display: flex; align-items: center; flex: 1;">
                            <div class="transaction-icon ${typeClass}">
                                <i class="${getTransactionIcon(tx.type)}"></i>
                            </div>
                            <div class="transaction-details">
                                <div class="transaction-description">
                                    ${tx.description || 'No description'}
                                </div>
                                <div class="transaction-meta">
                                    <span class="transaction-date">
                                        <i class="bi bi-calendar3"></i>
                                        ${formatDate(tx.date)}
                                    </span>
                                    <span class="badge-modern ${categoryBadgeClass}">
                                        ${tx.category || 'Uncategorized'}
                                    </span>
                                    <span class="badge-type ${typeClass}">
                                        ${getTypeName(tx.type)}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="transaction-amount ${typeClass}">
                            ${tx.type === 'expense' ? '-' : '+'}${formatCurrency(tx.amount)}
                        </div>
                    </div>
                    ${tx.tags ? `<div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.5rem;">
                        <i class="bi bi-tags"></i> ${tx.tags}
                    </div>` : ''}
                    <div style="display: flex; gap: 0.75rem; margin-top: 1rem;">
                        <a href="edit-entry.php?id=${tx.id}" class="btn-modern btn-secondary-modern" style="flex: 1;">
                            <i class="bi bi-pencil-square"></i>
                            <span>Edit</span>
                        </a>
                        <button onclick="deleteTransaction(${tx.id}, '${(tx.description || 'this transaction').replace(/'/g, "\\'")}');" class="btn-modern btn-danger-modern" style="flex: 1;">
                            <i class="bi bi-trash"></i>
                            <span>Delete</span>
                        </button>
                    </div>
                `;
                cardView.appendChild(card);
            });
        }

        // ===================================================================
        // PAGINATION FUNCTIONS
        // ===================================================================
        function nextPage() {
            const totalPages = Math.ceil(filteredTransactions.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderTransactions();
                scrollToTop();
            }
        }

        function previousPage() {
            if (currentPage > 1) {
                currentPage--;
                renderTransactions();
                scrollToTop();
            }
        }

        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // ===================================================================
        // DELETE TRANSACTION
        // ===================================================================
        function deleteTransaction(id, description) {
            if (!confirm(`Are you sure you want to delete "${description}"?\n\nThis action cannot be undone.`)) {
                return;
            }
            
            // Show loading state
            const originalText = event.target.innerHTML;
            event.target.innerHTML = '<span class="loading-spinner"></span>';
            event.target.disabled = true;

            fetch('backend/transactions/delete_transaction.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    event.target.innerHTML = originalText;
                    event.target.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete transaction. Please try again.');
                event.target.innerHTML = originalText;
                event.target.disabled = false;
            });
        }

        // ===================================================================
        // EVENT LISTENERS
        // ===================================================================
        document.getElementById('searchInput').addEventListener('input', applyFilters);
        document.getElementById('typeFilter').addEventListener('change', applyFilters);
        document.getElementById('monthFilter').addEventListener('change', applyFilters);
        document.getElementById('sortFilter').addEventListener('change', applyFilters);
        
        document.getElementById('itemsPerPageSelect').addEventListener('change', function() {
            itemsPerPage = parseInt(this.value);
            currentPage = 1;
            renderTransactions();
        });

        // ===================================================================
        // MOBILE MENU TOGGLE
        // ===================================================================
        function toggleMenu() {
            document.getElementById("mobileSidebar").classList.toggle("active");
            document.getElementById("sidebarOverlay").classList.toggle("active");
        }

        // ===================================================================
        // THEME TOGGLE
        // ===================================================================
        const themeToggle = document.getElementById('theme-toggle');
        const setTheme = (theme) => {
            document.documentElement.setAttribute('data-theme', theme);
            themeToggle.innerHTML = theme === 'dark' 
                ? '<i class="fas fa-sun"></i>' 
                : '<i class="fas fa-moon"></i>';
            localStorage.setItem('theme', theme);
        };

        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                setTheme(currentTheme === 'dark' ? 'light' : 'dark');
            });

            setTheme(localStorage.getItem('theme') || 'light');
        }

        // ===================================================================
        // LOGOUT FUNCTIONALITY
        // ===================================================================
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.getElementById('logout-btn');
            
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    if (confirm('Are you sure you want to logout?')) {
                        window.location.href = 'login-pages/login.php';
                    }
                });
            }
        });

        // ===================================================================
        // INITIALIZATION
        // ===================================================================
        populateMonthFilter();
        applyFilters();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
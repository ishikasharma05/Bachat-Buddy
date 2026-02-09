<?php 
session_start(); 

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: login-pages/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bachat Buddy | Add Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="components/style.css" rel="stylesheet">
    <style>
        /* ===================================================================
           ENHANCED DESIGN SYSTEM - MATCHING TRANSACTION.PHP
           ================================================================ */
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

        :root {
            /* Primary Colors */
            --color-primary-500: #00bcd4;
            --color-primary-600: #00acc1;
            --color-primary-700: #0097a7;
            
            /* Semantic Colors */
            --color-success-500: #4caf50;
            --color-success-600: #43a047;
            --color-danger-500: #f44336;
            --color-danger-600: #e53935;
            --color-income-green: #10b981;
            --color-income-light: #d1fae5;
            --color-expense-red: #ef4444;
            --color-expense-light: #fee2e2;
            --color-savings-blue: #3b82f6;
            --color-savings-light: #dbeafe;
            --color-withdraw-amber: #f59e0b;
            --color-withdraw-light: #fef3c7;
            
            /* Neutral Colors */
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
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

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
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.4);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.6), 0 10px 10px -5px rgba(0, 0, 0, 0.5);
        }

        .layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ===================================================================
           ENHANCED SIDEBAR - MATCHING TRANSACTION.PHP
           ================================================================ */
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

        /* ===================================================================
           ENHANCED HEADER - MATCHING TRANSACTION.PHP
           ================================================================ */
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

        /* ===================================================================
           TRANSACTION TYPE SELECTOR
           ================================================================ */
        .transaction-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .type-option {
            background: var(--bg-card);
            border: 2px solid var(--border-light);
            border-radius: 16px;
            padding: 1.5rem 1rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .type-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .type-option:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .type-option:hover::before {
            opacity: 1;
        }

        .type-option.active {
            transform: translateY(-4px) scale(1.02);
        }

        .type-option.income {
            border-color: var(--color-income-green);
            background: linear-gradient(135deg, var(--color-income-light), rgba(16, 185, 129, 0.05));
        }

        .type-option.income.active {
            background: linear-gradient(135deg, var(--color-income-green), #059669);
            border-color: var(--color-income-green);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
        }

        .type-option.expense {
            border-color: var(--color-expense-red);
            background: linear-gradient(135deg, var(--color-expense-light), rgba(239, 68, 68, 0.05));
        }

        .type-option.expense.active {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border-color: var(--color-expense-red);
            box-shadow: 0 8px 24px rgba(239, 68, 68, 0.4);
        }

        .type-option.savings {
            border-color: var(--color-savings-blue);
            background: linear-gradient(135deg, var(--color-savings-light), rgba(59, 130, 246, 0.05));
        }

        .type-option.savings.active {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-color: var(--color-savings-blue);
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
        }

        .type-option.withdraw {
            border-color: var(--color-withdraw-amber);
            background: linear-gradient(135deg, var(--color-withdraw-light), rgba(245, 158, 11, 0.05));
        }

        .type-option.withdraw.active {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-color: var(--color-withdraw-amber);
            box-shadow: 0 8px 24px rgba(245, 158, 11, 0.4);
        }

        .type-icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            display: block;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .type-option:hover .type-icon {
            transform: scale(1.1);
        }

        .type-option.active .type-icon {
            transform: scale(1.15);
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        .type-option.income .type-icon { color: var(--color-income-green); }
        .type-option.expense .type-icon { color: var(--color-expense-red); }
        .type-option.savings .type-icon { color: var(--color-savings-blue); }
        .type-option.withdraw .type-icon { color: var(--color-withdraw-amber); }

        .type-option.active .type-icon {
            color: white;
        }

        .type-label {
            font-weight: 700;
            font-size: 0.9375rem;
            display: block;
            transition: color 0.3s ease;
        }

        .type-option.income .type-label { color: var(--color-income-green); }
        .type-option.expense .type-label { color: var(--color-expense-red); }
        .type-option.savings .type-label { color: var(--color-savings-blue); }
        .type-option.withdraw .type-label { color: var(--color-withdraw-amber); }

        .type-option.active .type-label {
            color: white;
        }

        /* ===================================================================
           FORM SECTIONS
           ================================================================ */
        .transaction-card {
            max-width: 800px;
            margin: 0 auto;
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
        }

        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-light);
        }

        .form-section:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            background: linear-gradient(135deg, var(--color-primary-500), var(--color-primary-600));
            color: white;
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        /* ===================================================================
           ENHANCED INPUT FIELDS
           ================================================================ */
        .input-group-modern {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.125rem;
            pointer-events: none;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .input-modern {
            width: 100%;
            background-color: var(--input-bg);
            border: 2px solid var(--border-light);
            border-radius: 12px;
            padding: 0.875rem 1rem 0.875rem 3rem;
            color: var(--text-primary);
            font-size: 0.9375rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-modern:focus {
            outline: none;
            background-color: var(--bg-card);
            border-color: var(--color-primary-500);
            box-shadow: 0 0 0 4px rgba(0, 188, 212, 0.1);
        }

        .input-modern:focus + .input-icon {
            color: var(--color-primary-500);
            transform: translateY(-50%) scale(1.1);
        }

        .input-modern::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
        }

        .input-modern.error {
            border-color: var(--color-expense-red);
            background-color: var(--color-expense-light);
        }

        .input-modern.success {
            border-color: var(--color-income-green);
            background-color: var(--color-income-light);
        }

        .validation-message {
            display: none;
            margin-top: 0.5rem;
            padding: 0.625rem 1rem;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 600;
            align-items: center;
            gap: 0.5rem;
            animation: slideDown 0.3s ease;
        }

        .validation-message.show {
            display: flex;
        }

        .validation-message.error {
            background: var(--color-expense-light);
            color: #991b1b;
        }

        .validation-message.success {
            background: var(--color-income-light);
            color: #065f46;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-label-modern {
            display: block;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 0.625rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .select-modern {
            width: 100%;
            background-color: var(--input-bg);
            border: 2px solid var(--border-light);
            border-radius: 12px;
            padding: 0.875rem 1rem 0.875rem 3rem;
            color: var(--text-primary);
            font-size: 0.9375rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
        }

        .select-modern:focus {
            outline: none;
            background-color: var(--bg-card);
            border-color: var(--color-primary-500);
            box-shadow: 0 0 0 4px rgba(0, 188, 212, 0.1);
        }

        textarea.input-modern {
            resize: vertical;
            min-height: 100px;
            padding-top: 1rem;
        }

        /* ===================================================================
           SUBMIT BUTTON
           ================================================================ */
        .submit-btn-container {
            margin-top: 2.5rem;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--color-primary-500), var(--color-primary-600));
            color: white;
            border: none;
            border-radius: 14px;
            padding: 1.125rem 2rem;
            font-size: 1.0625rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            box-shadow: 0 6px 20px rgba(0, 188, 212, 0.3);
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 188, 212, 0.4);
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-loading {
            pointer-events: none;
        }

        .btn-spinner {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-success {
            background: linear-gradient(135deg, var(--color-income-green), #059669) !important;
        }

        /* ===================================================================
           TOAST NOTIFICATIONS
           ================================================================ */
        .toast-container {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .toast {
            background: white;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            min-width: 320px;
            max-width: 400px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            animation: toastSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid;
        }

        .toast.success {
            border-left-color: var(--color-income-green);
        }

        .toast.error {
            border-left-color: var(--color-expense-red);
        }

        .toast-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
            flex-shrink: 0;
        }

        .toast.success .toast-icon {
            background: var(--color-income-light);
            color: var(--color-income-green);
        }

        .toast.error .toast-icon {
            background: var(--color-expense-light);
            color: var(--color-expense-red);
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            font-size: 0.9375rem;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .toast-message {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0;
            font-size: 1.25rem;
            line-height: 1;
            transition: color 0.2s ease;
        }

        .toast-close:hover {
            color: var(--text-primary);
        }

        @keyframes toastSlideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .toast.hiding {
            animation: toastSlideOut 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes toastSlideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        /* Dark mode toast */
        [data-theme="dark"] .toast {
            background: var(--bg-card);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        /* ===================================================================
           RESPONSIVE DESIGN
           ================================================================ */
        @media (max-width: 991px) {
            .sidebar.d-lg-block {
                display: none !important;
            }

            .main-body {
                padding: 1.5rem 1rem;
            }
        }

        @media (max-width: 767px) {
            .transaction-selector {
                grid-template-columns: repeat(2, 1fr);
            }

            .transaction-card {
                padding: 1.5rem;
            }

            .section-header {
                margin-bottom: 1rem;
            }

            .form-section {
                margin-bottom: 1.5rem;
                padding-bottom: 1.5rem;
            }
        }

        @media (max-width: 575px) {
            .transaction-selector {
                grid-template-columns: 1fr;
            }

            .toast-container {
                top: 1rem;
                right: 1rem;
                left: 1rem;
            }

            .toast {
                min-width: auto;
            }
        }

        /* ===================================================================
           ACCESSIBILITY
           ================================================================ */
        :focus-visible {
            outline: 3px solid var(--color-primary-500);
            outline-offset: 2px;
        }

        /* Custom Scrollbar */
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
    <div class="toast-container" id="toastContainer"></div>

    <div class="layout">
        <div class="sidebar d-none d-lg-block">
            <div>
                <div class="brand">
                    <i class="bi bi-piggy-bank-fill"></i>
                    <span>Bachat Buddy</span>
                </div>
                <ul class="nav flex-column">
                    <li><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link active" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>

        <div id="mobileSidebar" class="mobile-sidebar d-lg-none">
            <div class="p-4">
                <div class="brand d-flex align-items-center justify-content-between mb-4">
                    <span><i class="bi bi-piggy-bank-fill"></i> Bachat Buddy</span>
                    <button onclick="toggleMenu()" class="btn-close border-0 bg-transparent text-muted fs-4" aria-label="Close menu">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <ul class="nav flex-column">
                    <li><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link active" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>
        <div id="sidebarOverlay" class="sidebar-overlay d-lg-none" onclick="toggleMenu()"></div>

        <div class="main-content">
            <?php include 'components/header.php'; ?>

            <div class="main-body">
                <div class="container">
                    <div class="text-center mb-5">
                        <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                            <i class="bi bi-journal-plus" style="color: var(--color-primary-500);"></i>
                            Add New Transaction
                        </h1>
                        <p style="color: var(--text-muted); font-size: 1.0625rem;">
                            Track your financial activity with ease
                        </p>
                    </div>

                    <div class="transaction-selector">
                        <div class="type-option income active" data-type="income">
                            <i class="bi bi-arrow-down-circle-fill type-icon"></i>
                            <span class="type-label">Income</span>
                        </div>
                        <div class="type-option expense" data-type="expense">
                            <i class="bi bi-arrow-up-circle-fill type-icon"></i>
                            <span class="type-label">Expense</span>
                        </div>
                        <div class="type-option savings" data-type="savings">
                            <i class="bi bi-piggy-bank-fill type-icon"></i>
                            <span class="type-label">Savings</span>
                        </div>
                        <div class="type-option withdraw" data-type="withdraw_savings">
                            <i class="bi bi-arrow-counterclockwise type-icon"></i>
                            <span class="type-label">Withdraw</span>
                        </div>
                    </div>

                    <div class="transaction-card">
                        <form id="transactionForm">
                            <input type="hidden" id="transactionType" value="income">

                            <div class="form-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="bi bi-cash-stack"></i>
                                    </div>
                                    <h3 class="section-title">Amount & Date</h3>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label-modern">Amount *</label>
                                        <div class="input-group-modern">
                                            <input 
                                                type="number" 
                                                step="0.01" 
                                                class="input-modern" 
                                                placeholder="0.00" 
                                                id="amount" 
                                                required
                                                min="0.01"
                                            >
                                            <i class="bi bi-currency-rupee input-icon"></i>
                                        </div>
                                        <div class="validation-message error" id="amountError">
                                            <i class="bi bi-exclamation-circle"></i>
                                            <span>Please enter a valid amount</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-modern">Date *</label>
                                        <div class="input-group-modern">
                                            <input 
                                                type="date" 
                                                class="input-modern" 
                                                id="date" 
                                                required
                                            >
                                            <i class="bi bi-calendar-event input-icon"></i>
                                        </div>
                                        <div class="validation-message error" id="dateError">
                                            <i class="bi bi-exclamation-circle"></i>
                                            <span>Please select a date</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="bi bi-tags-fill"></i>
                                    </div>
                                    <h3 class="section-title">Category & Details</h3>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label-modern">Category *</label>
                                    <div class="input-group-modern">
                                        <select class="select-modern" id="category" required>
                                            <option value="" disabled selected>Select category</option>
                                        </select>
                                        <i class="bi bi-grid-3x3-gap input-icon"></i>
                                    </div>
                                    <div class="validation-message error" id="categoryError">
                                        <i class="bi bi-exclamation-circle"></i>
                                        <span>Please select a category</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label-modern">Description</label>
                                    <div class="input-group-modern">
                                        <textarea 
                                            class="input-modern" 
                                            id="description" 
                                            rows="3" 
                                            placeholder="What was this for? (Optional)"
                                            style="padding-left: 3rem;"
                                        ></textarea>
                                        <i class="bi bi-chat-left-text input-icon" style="top: 1.5rem;"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="bi bi-bookmark-fill"></i>
                                    </div>
                                    <h3 class="section-title">Tags (Optional)</h3>
                                </div>

                                <div class="input-group-modern">
                                    <input 
                                        type="text" 
                                        class="input-modern" 
                                        id="tags" 
                                        placeholder="e.g., food, travel, urgent, monthly"
                                    >
                                    <i class="bi bi-hash input-icon"></i>
                                </div>
                                <small style="color: var(--text-muted); font-size: 0.8125rem; display: block; margin-top: 0.5rem;">
                                    <i class="bi bi-info-circle"></i> Separate tags with commas for better organization
                                </small>
                            </div>

                            <div class="submit-btn-container">
                                <button type="submit" class="submit-btn" id="submitBtn">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span id="btnText">Save Transaction</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php include 'components/footer.php'; ?>
            </div>
        </div>
    </div>

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

    <script>
        // Category options for each transaction type
        const categoryOptions = {
            income: [
                { value: 'Salary', label: '💼 Salary' },
                { value: 'Freelance', label: '💻 Freelance' },
                { value: 'Business', label: '🏢 Business' },
                { value: 'Investment Returns', label: '📈 Investment Returns' },
                { value: 'Gift', label: '🎁 Gift' },
                { value: 'Bonus', label: '🎉 Bonus' },
                { value: 'Refund', label: '💰 Refund' },
                { value: 'Other Income', label: '➕ Other Income' }
            ],
            expense: [
                { value: 'Groceries', label: '🛒 Groceries' },
                { value: 'Dining Out', label: '🍽️ Dining Out' },
                { value: 'Transportation', label: '🚗 Transportation' },
                { value: 'Rent', label: '🏠 Rent' },
                { value: 'Utilities', label: '💡 Utilities' },
                { value: 'Bills', label: '📄 Bills' },
                { value: 'Healthcare', label: '🏥 Healthcare' },
                { value: 'Entertainment', label: '🎬 Entertainment' },
                { value: 'Shopping', label: '🛍️ Shopping' },
                { value: 'Education', label: '📚 Education' },
                { value: 'Fitness', label: '💪 Fitness' },
                { value: 'Travel', label: '✈️ Travel' },
                { value: 'Subscriptions', label: '📱 Subscriptions' },
                { value: 'Other Expense', label: '➖ Other Expense' }
            ],
            savings: [
                { value: 'Emergency Fund', label: '🆘 Emergency Fund' },
                { value: 'Retirement', label: '👴 Retirement' },
                { value: 'Investment', label: '📊 Investment' },
                { value: 'Vacation', label: '🏖️ Vacation' },
                { value: 'Home Down Payment', label: '🏡 Home Down Payment' },
                { value: 'Education Fund', label: '🎓 Education Fund' },
                { value: 'General Savings', label: '🐷 General Savings' },
                { value: 'Other Savings', label: '💎 Other Savings' }
            ],
            withdraw_savings: [
                { value: 'Emergency', label: '🚨 Emergency' },
                { value: 'Large Purchase', label: '🛒 Large Purchase' },
                { value: 'Investment Opportunity', label: '💼 Investment Opportunity' },
                { value: 'Debt Payment', label: '💳 Debt Payment' },
                { value: 'Transfer to Checking', label: '🔄 Transfer to Checking' },
                { value: 'Other Withdrawal', label: '💸 Other Withdrawal' }
            ]
        };

        const typeOptions = document.querySelectorAll('.type-option');
        const transactionTypeInput = document.getElementById('transactionType');
        const categorySelect = document.getElementById('category');

        function updateCategories(type) {
            categorySelect.innerHTML = '<option value="" disabled selected>Select category</option>';
            
            const options = categoryOptions[type] || [];
            options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.label;
                categorySelect.appendChild(option);
            });

            categorySelect.value = '';
            clearValidation(categorySelect);
        }

        typeOptions.forEach(option => {
            option.addEventListener('click', function() {
                typeOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                
                const type = this.dataset.type;
                transactionTypeInput.value = type;
                updateCategories(type);
            });
        });

        // Validation functions
        const form = document.getElementById('transactionForm');
        const amountInput = document.getElementById('amount');
        const dateInput = document.getElementById('date');
        const categoryInput = document.getElementById('category');

        function showError(input, errorId) {
            input.classList.add('error');
            input.classList.remove('success');
            const errorMsg = document.getElementById(errorId);
            if (errorMsg) errorMsg.classList.add('show');
        }

        function showSuccess(input, errorId) {
            input.classList.remove('error');
            input.classList.add('success');
            const errorMsg = document.getElementById(errorId);
            if (errorMsg) errorMsg.classList.remove('show');
        }

        function clearValidation(input) {
            input.classList.remove('error', 'success');
            const errorId = input.id + 'Error';
            const errorMsg = document.getElementById(errorId);
            if (errorMsg) errorMsg.classList.remove('show');
        }

        amountInput.addEventListener('blur', function() {
            const value = parseFloat(this.value);
            if (!this.value || isNaN(value) || value <= 0) {
                showError(this, 'amountError');
            } else {
                showSuccess(this, 'amountError');
            }
        });

        amountInput.addEventListener('input', function() {
            if (this.value) {
                const value = parseFloat(this.value);
                if (!isNaN(value) && value > 0) {
                    showSuccess(this, 'amountError');
                }
            }
        });

        dateInput.addEventListener('blur', function() {
            if (!this.value) {
                showError(this, 'dateError');
            } else {
                showSuccess(this, 'dateError');
            }
        });

        dateInput.addEventListener('change', function() {
            if (this.value) showSuccess(this, 'dateError');
        });

        categoryInput.addEventListener('blur', function() {
            if (!this.value) {
                showError(this, 'categoryError');
            } else {
                showSuccess(this, 'categoryError');
            }
        });

        categoryInput.addEventListener('change', function() {
            if (this.value) showSuccess(this, 'categoryError');
        });

        // Toast notification system
        function showToast(type, title, message) {
            const container = document.getElementById('toastContainer');
            
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill'}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="dismissToast(this)">
                    <i class="bi bi-x"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                dismissToast(toast.querySelector('.toast-close'));
            }, 5000);
        }

        function dismissToast(button) {
            const toast = button.closest('.toast');
            toast.classList.add('hiding');
            setTimeout(() => toast.remove(), 400);
        }

        // Form submission
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            let isValid = true;

            const amount = parseFloat(amountInput.value);
            if (!amountInput.value || isNaN(amount) || amount <= 0) {
                showError(amountInput, 'amountError');
                isValid = false;
            }

            if (!dateInput.value) {
                showError(dateInput, 'dateError');
                isValid = false;
            }

            if (!categoryInput.value) {
                showError(categoryInput, 'categoryError');
                isValid = false;
            }

            if (!isValid) {
                showToast('error', 'Validation Error', 'Please fill in all required fields correctly.');
                return;
            }

            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <div class="btn-spinner"></div>
                <span>Saving...</span>
            `;

            const payload = {
                type: transactionTypeInput.value,
                amount: parseFloat(amountInput.value),
                date: dateInput.value,
                category: categoryInput.value,
                description: document.getElementById('description').value,
                tags: document.getElementById('tags').value
            };

            try {
                const res = await fetch('/Bachat-Buddy/Bachat-Buddy/Bachat-Buddy/backend/transactions/add_transaction.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const contentType = res.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server returned invalid response');
                }

                const data = await res.json();

                if (data.status === 'success') {
                    submitBtn.classList.remove('btn-loading');
                    submitBtn.classList.add('btn-success');
                    submitBtn.innerHTML = `
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Saved Successfully!</span>
                    `;

                    showToast('success', 'Transaction Saved!', data.message || 'Your transaction has been recorded successfully.');

                    setTimeout(() => {
                        form.reset();
                        dateInput.valueAsDate = new Date();
                        
                        clearValidation(amountInput);
                        clearValidation(dateInput);
                        clearValidation(categoryInput);

                        typeOptions.forEach(opt => opt.classList.remove('active'));
                        typeOptions[0].classList.add('active');
                        transactionTypeInput.value = 'income';
                        updateCategories('income');

                        submitBtn.classList.remove('btn-success');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = `
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Save Transaction</span>
                        `;
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Failed to save transaction');
                }
            } catch (err) {
                submitBtn.classList.remove('btn-loading');
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Save Transaction</span>
                `;

                showToast('error', 'Error Saving Transaction', err.message || 'Please check your connection and try again.');
            }
        });

        function toggleMenu() {
            document.getElementById("mobileSidebar").classList.toggle("active");
            document.getElementById("sidebarOverlay").classList.toggle("active");
        }

        // Theme toggle
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            const setTheme = (isDark) => {
                if (isDark) {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
                } else {
                    document.documentElement.setAttribute('data-theme', 'light');
                    themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
                }
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            };

            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme === 'dark');

            themeToggle.addEventListener('click', () => {
                const isNowDark = document.documentElement.getAttribute('data-theme') !== 'dark';
                setTheme(isNowDark);
            });
        }

        // Logout
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to logout?')) {
                        window.location.href = 'add-entry.php?action=logout';
                    }
                });
            }
        });

        // Initialize
        document.getElementById('date').valueAsDate = new Date();
        updateCategories('income');
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
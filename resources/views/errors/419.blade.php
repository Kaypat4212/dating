<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .error-container {
            max-width: 600px;
            margin: 2rem;
        }
        .error-card {
            background: white;
            border-radius: 20px;
            padding: 3rem 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }
        .error-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: pulse 2s ease-in-out infinite;
        }
        .error-icon i {
            font-size: 2.5rem;
            color: white;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .error-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }
        .error-code {
            font-size: 1rem;
            color: #718096;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .error-message {
            color: #4a5568;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .causes-list {
            text-align: left;
            background: #f7fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .causes-list h5 {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .causes-list ul {
            margin: 0;
            padding-left: 1.5rem;
        }
        .causes-list li {
            color: #4a5568;
            margin-bottom: 0.75rem;
            line-height: 1.5;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .btn-outline {
            background: white;
            border: 2px solid #e2e8f0;
            color: #4a5568;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.2s;
        }
        .btn-outline:hover {
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-2px);
        }
        .footer-note {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <i class="bi bi-hourglass-split"></i>
            </div>
            
            <div class="error-code">ERROR 419</div>
            <h1 class="error-title">Session Expired</h1>
            
            <p class="error-message">
                Your session has expired or the security token is invalid. 
                This happens to protect your account security.
            </p>

            <div class="causes-list">
                <h5>
                    <i class="bi bi-info-circle-fill" style="color: #667eea;"></i>
                    Common Causes:
                </h5>
                <ul>
                    <li><strong>Inactive for too long:</strong> You've been away from the app for an extended period</li>
                    <li><strong>Multiple tabs/devices:</strong> Logging in on another device or tab invalidated this session</li>
                    <li><strong>Browser back button:</strong> Using the back button after submitting a form</li>
                    <li><strong>Cleared cookies:</strong> Browser cookies or cache were recently cleared</li>
                    <li><strong>Page was cached:</strong> You're viewing an old version of the page</li>
                </ul>
            </div>

            <div class="action-buttons">
                <button onclick="window.location.reload()" class="btn btn-primary">
                    <i class="bi bi-arrow-clockwise me-2"></i>Refresh Page
                </button>
                <a href="{{ route('login') }}" class="btn btn-outline">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login Again
                </a>
            </div>

            <div class="footer-note">
                <i class="bi bi-shield-check me-1"></i>
                Don't worry - your data is safe. Simply reload the page or log in again to continue.
            </div>
        </div>
    </div>

    <script>
        // Auto-reload after 5 seconds
        let countdown = 5;
        const timer = setInterval(() => {
            countdown--;
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.reload();
            }
        }, 1000);

        // If user is authenticated, try to restore the previous action
        @auth
        if (document.referrer && document.referrer.includes('{{ url('/') }}')) {
            // Store the attempted action for after reload
            sessionStorage.setItem('attempted_action', document.referrer);
        }
        @endauth
    </script>
</body>
</html>

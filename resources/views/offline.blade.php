<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#4f98a3">
    <title>Offline — DayOS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #171614;
            color: #f5f3ef;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            text-align: center;
            max-width: 360px;
        }
        .icon-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .pulse-ring {
            position: absolute;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 2px solid rgba(79, 152, 163, 0.3);
            animation: pulse-ring 3s ease-in-out infinite;
        }
        .pulse-ring:nth-child(2) {
            width: 180px;
            height: 180px;
            animation-delay: 0.5s;
            border-color: rgba(79, 152, 163, 0.15);
        }
        @keyframes pulse-ring {
            0%, 100% { transform: scale(0.95); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.5; }
        }
        .icon-img {
            width: 96px;
            height: 96px;
            border-radius: 20px;
            position: relative;
            z-index: 1;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 0.75rem;
            color: #f5f3ef;
        }
        p {
            font-size: 0.875rem;
            line-height: 1.6;
            color: rgba(245, 243, 239, 0.6);
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
            background: #4f98a3;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(79, 152, 163, 0.3);
        }
        .btn:hover {
            background: #3d8490;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79, 152, 163, 0.4);
        }
        .btn:active {
            transform: scale(0.97);
        }
        .btn svg {
            width: 16px;
            height: 16px;
        }
        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            margin-right: 6px;
            animation: blink 2s infinite;
        }
        .status {
            font-size: 0.75rem;
            color: rgba(245, 243, 239, 0.4);
            margin-top: 2rem;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-wrapper">
            <div class="pulse-ring"></div>
            <div class="pulse-ring"></div>
            <img src="/icons/icon-192.png" alt="DayOS" class="icon-img">
        </div>

        <h1>You're offline</h1>
        <p>DayOS needs a connection to sync your tasks. Your last saved dashboard is available below.</p>

        <a href="/admin/today" class="btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            View Cached Dashboard
        </a>

        <div class="status">
            <span class="status-dot"></span>
            Waiting for connection
        </div>
    </div>
</body>
</html>

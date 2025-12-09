<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'School Payroll') }}</title>

        <!-- Google Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            * {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            }
            
            body {
                background: linear-gradient(135deg, #ccd4fa 0%, #e1e7ff 100%);
                background-attachment: fixed;
                min-height: 100vh;
            }
            
            .login-card {
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 20px;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            }
            
            .login-input {
                background: rgba(255, 255, 255, 0.9);
                border: 1px solid rgba(0, 0, 0, 0.1);
                border-radius: 12px;
                padding: 14px 16px;
                font-size: 15px;
                width: 100%;
                transition: all 0.2s ease;
            }
            
            .login-input:focus {
                outline: none;
                border-color: #0071e3;
                box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.15);
                background: white;
            }
            
            .login-label {
                font-size: 13px;
                font-weight: 600;
                color: #1d1d1f;
                margin-bottom: 6px;
                display: block;
            }
            
            .login-btn {
                background: linear-gradient(135deg, #0071e3 0%, #0077ed 100%);
                color: white;
                padding: 14px 28px;
                border-radius: 12px;
                font-weight: 600;
                font-size: 15px;
                border: none;
                cursor: pointer;
                transition: all 0.2s ease;
                width: 100%;
            }
            
            .login-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(0, 113, 227, 0.3);
            }
            
            .login-link {
                color: #0071e3;
                font-size: 13px;
                text-decoration: none;
                font-weight: 500;
            }
            
            .login-link:hover {
                text-decoration: underline;
            }
            
            .checkbox-modern {
                width: 18px;
                height: 18px;
                border-radius: 5px;
                accent-color: #0071e3;
            }
            
            .logo-container {
                width: 80px;
                height: 80px;
                background: rgba(255, 255, 255, 0.9);
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                margin-bottom: 24px;
            }
            
            .error-text {
                color: #ef4444;
                font-size: 12px;
                margin-top: 6px;
            }
        </style>
    </head>
    <body>
        <div class="min-h-screen flex flex-col justify-center items-center p-6">
            <!-- Logo -->
            <div class="logo-container">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#0071e3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
            
            <!-- Title -->
            <h1 style="font-size: 28px; font-weight: 700; color: white; margin-bottom: 8px; text-shadow: 0 2px 10px rgba(0,0,0,0.2);">School Payroll</h1>
            <p style="font-size: 14px; color: rgba(255,255,255,0.8); margin-bottom: 32px;">Management System</p>

            <!-- Card -->
            <div class="login-card w-full sm:max-w-md px-8 py-10">
                {{ $slot }}
            </div>
            
            <!-- Footer -->
            <p style="font-size: 12px; color: rgba(0, 0, 0, 0.6); margin-top: 32px;">
                &copy; {{ date('Y') }} School Payroll. Design by elfahmie.
            </p>
        </div>
    </body>
</html>

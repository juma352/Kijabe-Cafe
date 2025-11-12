<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login • Kijabe POS</title>
    <style>
        /* Inline critical CSS to prevent FOUC */
        *{box-sizing:border-box}
        html,body{height:100%;margin:0;font-family:Inter,Segoe UI,Roboto,Arial,sans-serif;background:#000}
        .bg{position:fixed;inset:0;background-size:cover;background-position:center;filter:brightness(0.45);z-index:0;transition:opacity 1s ease-in-out}
        .bg.fade{opacity:0}
        .topbar{position:fixed;right:40px;top:24px;z-index:3}
        .logo-text{color:#fff;font-weight:700;letter-spacing:2px;font-size:20px}
        .login-main{position:relative;min-height:100vh;display:flex;align-items:center;justify-content:center;z-index:2}
        .login-card{width:380px;background:rgba(0,0,0,0.55);padding:32px;border-radius:8px;color:#fff;box-shadow:0 10px 30px rgba(0,0,0,0.6)}
        .login-title{margin:0 0 18px 0;font-size:22px;display:flex;align-items:center}
        .login-form .field{position:relative;margin-bottom:14px}
        .login-form input[type=text],.login-form input[type=email],.login-form input[type=password],.login-form select{width:100%;padding:12px 44px 12px 14px;border-radius:6px;border:1px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.03);color:#fff;font-size:15px}
        .field-icon{position:absolute;right:12px;top:50%;transform:translateY(-50%);opacity:0.9}
        .actions{display:flex;align-items:center;justify-content:space-between;margin-top:14px}
        .btn{background:#e74c3c;border:none;color:#fff;padding:10px 18px;border-radius:6px;cursor:pointer;font-weight:600}
        .links{margin-top:12px;display:flex;justify-content:space-between;font-size:13px}
        .links a{color:#f0f0f0;opacity:0.9;text-decoration:none}
        .error{color:#ff6b6b;margin-top:6px;font-size:13px}
        .remember input{margin-right:8px}
        @media(max-width:420px){.login-card{width:92%;padding:20px;margin:10px}}
        
        /* Slider dots indicator */
        .slider-dots{position:fixed;bottom:30px;left:50%;transform:translateX(-50%);display:flex;gap:8px;z-index:3}
        .dot{width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,0.4);cursor:pointer;transition:background 0.3s}
        .dot.active{background:rgba(255,255,255,0.9)}
    </style>
</head>
<body>
    <div class="bg" id="bgSlider"></div>
    <header class="topbar">
        <div class="logo-text">Kijabe Hospital P.O.S</div>
    </header>

    <main class="login-main">
        <section class="login-card">
            <h2 class="login-title">Login</h2>
            <form action="{{ route('login') }}" method="POST" class="login-form">
                @csrf

                <div class="field">
                    <input type="text" name="email" placeholder="Username/Email" value="{{ old('email') }}" required autofocus>
                    <span class="field-icon">👤</span>
                    @error('email') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <input type="password" name="password" placeholder="Password" required>
                    <span class="field-icon">🔒</span>
                    @error('password') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="actions">
                    <label class="remember"><input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember</label>
                    <button type="submit" class="btn">Login</button>
                </div>

                <div class="links">
                    <a href="{{ route('register') }}">Register</a>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>
            </form>
        </section>
    </main>

    <!-- Slider dots indicator -->
    <div class="slider-dots" id="sliderDots"></div>

    <script>
        // Background image slider
        const images = [
            '/Images/KH 1.jpg',
            '/Images/KH-2.jpg', 
            '/Images/KH-4.jpg',
            '/Images/KH-5.jpg',
            '/Images/KH-6.jpg',
            '/Images/KH-7.jpg',
            '/Images/KH-8.jpg'
        ];
        
        let currentIndex = 0;
        const bgElement = document.getElementById('bgSlider');
        const dotsContainer = document.getElementById('sliderDots');
        
        // Create dots
        images.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.className = `dot ${index === 0 ? 'active' : ''}`;
            dot.addEventListener('click', () => goToSlide(index));
            dotsContainer.appendChild(dot);
        });
        
        const dots = document.querySelectorAll('.dot');
        
        function updateBackground() {
            bgElement.style.backgroundImage = `url('${images[currentIndex]}')`;
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentIndex);
            });
        }
        
        function nextSlide() {
            currentIndex = (currentIndex + 1) % images.length;
            updateBackground();
        }
        
        function goToSlide(index) {
            currentIndex = index;
            updateBackground();
        }
        
        // Initialize first image
        updateBackground();
        
        // Auto-advance every 4 seconds
        setInterval(nextSlide, 4000);
    </script>
</body>
</html>

            </div>

            <!-- /.col -->

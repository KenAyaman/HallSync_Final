<x-guest-layout>
    <div class="rex-login-shell">
        <div class="rex-login-head">
            <span class="rex-login-kicker">Secure Sign In</span>
            <h2 class="rex-login-title">Welcome back to HallSync</h2>
            <p class="rex-login-subtitle">
                Access the Rexhall residence system with your assigned account credentials.
            </p>
        </div>

        <x-auth-session-status class="rex-login-status" :status="session('status')" />

        @if ($errors->any())
            <div class="rex-login-error">
                <div class="rex-login-error-title">There was a problem with your sign in.</div>
                <ul class="rex-login-error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="rex-login-form" data-prevent-double-submit data-submitting-text="Signing In...">
            @csrf

            <div class="rex-login-field">
                <label for="email" class="rex-login-label">Email Address</label>
                <input
                    id="email"
                    class="rex-login-input"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="you@rexhall.com"
                    required
                    autofocus
                    autocomplete="username">
            </div>

            <div class="rex-login-field">
                <label for="password" class="rex-login-label">Password</label>
                <div class="rex-login-password-wrap">
                    <input
                        id="password"
                        class="rex-login-input rex-login-input-password"
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password">
                    <button type="button" class="rex-login-password-toggle" data-toggle-password="password">Show</button>
                </div>
            </div>

            <div class="rex-login-row">
                <label for="remember_me" class="rex-login-check">
                    <input id="remember_me" type="checkbox" name="remember">
                    <span>Keep me signed in</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="rex-login-link" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button type="submit" class="rex-login-btn">
                Sign In to HallSync
            </button>
        </form>

        <div class="rex-login-note">
            Dorm access is managed for Rexhall residents and authorized staff only.
        </div>
    </div>

    <style>
        .rex-login-shell {
            width: 100%;
            max-width: 460px;
            margin: 0 auto;
            color: #f3ece2;
        }

        .rex-login-head {
            margin-bottom: 22px;
        }

        .rex-login-kicker {
            display: inline-block;
            margin-bottom: 10px;
            color: #d6a85b;
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .rex-login-title {
            margin: 0;
            color: #f8f3ea;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 4vw, 2.7rem);
            line-height: 1.05;
        }

        .rex-login-subtitle {
            margin: 12px 0 0;
            color: #bfae95;
            line-height: 1.75;
            font-size: 0.96rem;
        }

        .rex-login-status {
            margin-bottom: 16px;
            padding: 13px 14px;
            border-radius: 16px;
            background: rgba(90, 138, 90, 0.14);
            border: 1px solid rgba(90, 138, 90, 0.22);
            color: #b7d3b7;
            font-size: 0.9rem;
        }

        .rex-login-error {
            margin-bottom: 16px;
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(224, 112, 96, 0.12);
            border: 1px solid rgba(224, 112, 96, 0.18);
            color: #f0bbb3;
        }

        .rex-login-error-title {
            font-weight: 700;
            margin-bottom: 8px;
        }

        .rex-login-error-list {
            margin: 0;
            padding-left: 18px;
            line-height: 1.65;
        }

        .rex-login-form {
            display: grid;
            gap: 18px;
        }

        .rex-login-field {
            display: grid;
            gap: 9px;
        }

        .rex-login-label {
            color: #d2c7b6;
            font-size: 0.84rem;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .rex-login-input {
            width: 100%;
            min-height: 54px;
            padding: 0 16px;
            border-radius: 16px;
            border: 1px solid rgba(214, 168, 91, 0.14);
            background: rgba(37, 39, 42, 0.9);
            color: #f8f3ea;
            outline: none;
            transition: 0.18s ease;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.02);
        }

        .rex-login-input::placeholder {
            color: #87765f;
        }

        .rex-login-input:focus {
            border-color: rgba(214, 168, 91, 0.38);
            box-shadow: 0 0 0 4px rgba(214, 168, 91, 0.08);
        }

        .rex-login-password-wrap {
            position: relative;
        }

        .rex-login-input-password {
            padding-right: 76px;
        }

        .rex-login-password-toggle {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #d6a85b;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
        }

        .rex-login-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .rex-login-check {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #c3b29a;
            font-size: 0.9rem;
        }

        .rex-login-check input {
            width: 16px;
            height: 16px;
            accent-color: #d6a85b;
        }

        .rex-login-link {
            color: #d6a85b;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .rex-login-link:hover {
            color: #f0d8af;
        }

        .rex-login-btn {
            min-height: 54px;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(90deg, #b8842f 0%, #d6a85b 100%);
            color: #ffffff;
            font-size: 0.96rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 14px 30px rgba(199, 150, 69, 0.28);
            transition: 0.2s ease;
        }

        .rex-login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 34px rgba(199, 150, 69, 0.32);
        }

        .rex-login-note {
            margin-top: 18px;
            color: #8f7d67;
            font-size: 0.85rem;
            line-height: 1.7;
        }

        @media (max-width: 640px) {
            .rex-login-shell {
                max-width: none;
            }

            .rex-login-title {
                font-size: 1.8rem;
            }

            .rex-login-subtitle,
            .rex-login-note {
                font-size: 0.88rem;
            }

            .rex-login-input,
            .rex-login-btn {
                min-height: 50px;
            }

            .rex-login-password-toggle {
                right: 12px;
                font-size: 0.76rem;
            }

            .rex-login-row {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach((button) => {
            button.addEventListener('click', () => {
                const field = document.getElementById(button.dataset.togglePassword);
                const isPassword = field.type === 'password';
                field.type = isPassword ? 'text' : 'password';
                button.textContent = isPassword ? 'Hide' : 'Show';
            });
        });
    </script>
</x-guest-layout>

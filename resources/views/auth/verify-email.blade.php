<x-guest-layout>
    <div class="rex-auth-shell">
        <div class="rex-auth-head">
            <span class="rex-auth-kicker">Email Verification</span>
            <h2 class="rex-auth-title">Verify your email</h2>
            <p class="rex-auth-subtitle">
                Before getting started, please confirm your email address using the link we sent.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="rex-auth-status">
                A new verification link has been sent to your email address.
            </div>
        @endif

        <div class="rex-auth-form">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="rex-auth-btn">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rex-auth-link-btn">
                    Log Out
                </button>
            </form>
        </div>
    </div>

    <style>
        .rex-auth-shell { width: 100%; max-width: 460px; margin: 0 auto; color: #f3ece2; }
        .rex-auth-head { margin-bottom: 22px; }
        .rex-auth-kicker { display: inline-block; margin-bottom: 10px; color: #d6a85b; font-size: 0.74rem; font-weight: 700; letter-spacing: 0.22em; text-transform: uppercase; }
        .rex-auth-title { margin: 0; color: #f8f3ea; font-family: 'Playfair Display', serif; font-size: clamp(2rem, 4vw, 2.7rem); line-height: 1.05; }
        .rex-auth-subtitle { margin: 12px 0 0; color: #bfae95; line-height: 1.75; font-size: 0.96rem; }
        .rex-auth-status { margin-bottom: 16px; padding: 13px 14px; border-radius: 16px; background: rgba(90, 138, 90, 0.14); border: 1px solid rgba(90, 138, 90, 0.22); color: #b7d3b7; font-size: 0.9rem; }
        .rex-auth-form { display: grid; gap: 14px; }
        .rex-auth-btn { min-height: 54px; width: 100%; border: 0; border-radius: 999px; background: linear-gradient(90deg, #b8842f 0%, #d6a85b 100%); color: #ffffff; font-size: 0.96rem; font-weight: 800; cursor: pointer; box-shadow: 0 14px 30px rgba(199, 150, 69, 0.28); transition: 0.2s ease; }
        .rex-auth-btn:hover { transform: translateY(-1px); box-shadow: 0 18px 34px rgba(199, 150, 69, 0.32); }
        .rex-auth-link-btn { width: 100%; min-height: 50px; border-radius: 999px; border: 1px solid rgba(214, 168, 91, 0.14); background: rgba(255,255,255,0.04); color: #d6c8b5; font-size: 0.92rem; font-weight: 700; cursor: pointer; transition: 0.2s ease; }
        .rex-auth-link-btn:hover { background: rgba(255,255,255,0.08); color: #fff6e7; }
    </style>
</x-guest-layout>

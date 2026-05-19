@extends('layouts.public')

@section('title', 'Reset Password | Priyam Finserv')

@section('content')
    <div class="auth-split">
        <!-- Left: Image Side -->
        <div class="auth-split-image">
            <h2 style="font-family: var(--font-serif); font-size: 2.2rem; margin-bottom: 1rem; line-height: 1.2; color: #fff;">Create a New<br>Password.</h2>
            <p style="font-size: 1rem; color: #a1a1aa; font-weight: 300; max-width: 380px; line-height: 1.7;">Choose a strong password to keep your financial data and documents secure. You'll be redirected to login once done.</p>
            <div style="display: flex; gap: 2rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.15);">
                <div>
                    <p style="font-size: 1.4rem; font-weight: 700; color: #fff; font-family: var(--font-sans);">256-bit</p>
                    <p style="font-size: 0.75rem; color: #71717a; text-transform: uppercase; letter-spacing: 1px;">Encryption</p>
                </div>
                <div>
                    <p style="font-size: 1.4rem; font-weight: 700; color: #fff; font-family: var(--font-sans);">Secure</p>
                    <p style="font-size: 0.75rem; color: #71717a; text-transform: uppercase; letter-spacing: 1px;">Reset Link</p>
                </div>
            </div>
        </div>

        <!-- Right: Form -->
        <div class="auth-split-form">
            <div style="width: 100%; max-width: 400px;">
                <div style="margin-bottom: 2rem;">
                    <h1 style="font-size: 1.8rem; margin-bottom: 0.3rem;">Set New Password</h1>
                    <p style="color: var(--text-muted); font-size: 0.92rem;">Enter your email and choose a strong new password.</p>
                </div>

                <form method="POST" action="{{ route('password.store') }}" style="display: flex; flex-direction: column; gap: 1.2rem;">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email -->
                    <div>
                        <label for="email" style="display: block; font-weight: 500; margin-bottom: 0.35rem; font-size: 0.85rem;">Email Address</label>
                        <div style="position: relative;">
                            <i class="fa-solid fa-envelope" style="position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%); color: #a1a1aa; font-size: 0.85rem;"></i>
                            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                                class="form-control" placeholder="name@company.com"
                                style="padding: 0.7rem 0.85rem 0.7rem 2.5rem;">
                        </div>
                        @error('email')
                            <div style="color: #dc2626; font-size: 0.8rem; margin-top: 0.3rem;">
                                <i class="fa-solid fa-circle-exclamation" style="margin-right: 3px;"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" style="display: block; font-weight: 500; margin-bottom: 0.35rem; font-size: 0.85rem;">New Password</label>
                        <div style="position: relative;">
                            <i class="fa-solid fa-lock" style="position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%); color: #a1a1aa; font-size: 0.85rem;"></i>
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                class="form-control" placeholder="Min. 8 characters"
                                style="padding: 0.7rem 0.85rem 0.7rem 2.5rem;">
                        </div>
                        @error('password')
                            <div style="color: #dc2626; font-size: 0.8rem; margin-top: 0.3rem;">
                                <i class="fa-solid fa-circle-exclamation" style="margin-right: 3px;"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" style="display: block; font-weight: 500; margin-bottom: 0.35rem; font-size: 0.85rem;">Confirm New Password</label>
                        <div style="position: relative;">
                            <i class="fa-solid fa-shield-halved" style="position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%); color: #a1a1aa; font-size: 0.85rem;"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                class="form-control" placeholder="Repeat new password"
                                style="padding: 0.7rem 0.85rem 0.7rem 2.5rem;">
                        </div>
                        @error('password_confirmation')
                            <div style="color: #dc2626; font-size: 0.8rem; margin-top: 0.3rem;">
                                <i class="fa-solid fa-circle-exclamation" style="margin-right: 3px;"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 0.85rem; margin-top: 0.5rem;">
                        <i class="fa-solid fa-key" style="margin-right: 5px;"></i> Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

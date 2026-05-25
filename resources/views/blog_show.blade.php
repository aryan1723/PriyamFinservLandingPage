@extends('layouts.public')

@section('title', $post->title . ' | Priyam Finserv')

@section('content')
    <article style="padding-bottom: 5rem; background: var(--bg-color); min-height: 100vh;">

        {{-- Spacer that pushes content below the fixed header. --}}
        {{-- JS in the layout will update --header-h custom property, --}}
        {{-- but we set a safe default of 100px here as fallback. --}}
        <div style="height: var(--header-h, 100px);"></div>

        <div class="container" style="max-width: 800px; margin: 0 auto; padding-top: 2rem;">

            <a href="{{ url('/blog') }}" style="display: block; color: var(--text-muted); text-decoration: none; font-size: 0.9rem; margin-bottom: 2rem; transition: color 0.3s;">
                <i class="fa-solid fa-arrow-left" style="margin-right: 0.4rem;"></i> Back to Insights
            </a>

            <div style="margin-bottom: 3rem;">
                <h1 style="font-size: clamp(2rem, 5vw, 3rem); line-height: 1.2; margin-bottom: 1.5rem; font-family: var(--font-serif); color: var(--primary-black);">{{ $post->title }}</h1>
                <div style="display: flex; align-items: center; gap: 1rem; color: var(--text-muted); font-size: 0.95rem;">
                    <div style="display: flex; align-items: center; gap: 0.8rem;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-black); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-family: var(--font-serif); flex-shrink: 0;">
                            P
                        </div>
                        <div>
                            <div style="font-weight: 600; color: var(--primary-black);">Priyam Finserv</div>
                            <div>{{ $post->created_at->format('M d, Y') }} &bull; {{ max(1, ceil(str_word_count($post->content) / 200)) }} min read</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($post->image_path)
                <div style="margin-bottom: 3rem; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-sm);">
                    <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}" style="width: 100%; height: auto; display: block; max-height: 500px; object-fit: cover;">
                </div>
            @endif

            <div style="font-size: 1.1rem; line-height: 1.8; color: #333; font-family: var(--font-sans);">
                {!! nl2br(e($post->content)) !!}
            </div>

            <div style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="font-weight: 500;">Share this insight</div>
                <div style="display: flex; gap: 1rem;">
                    <a href="#" style="width: 36px; height: 36px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #555; transition: all 0.3s;"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="#" style="width: 36px; height: 36px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #555; transition: all 0.3s;"><i class="fa-brands fa-twitter"></i></a>
                    <a href="mailto:?subject={{ urlencode($post->title) }}&body={{ urlencode(url()->current()) }}" style="width: 36px; height: 36px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #555; transition: all 0.3s;"><i class="fa-solid fa-envelope"></i></a>
                </div>
            </div>

        </div>
    </article>
@endsection

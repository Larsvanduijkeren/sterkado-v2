@extends('layouts.app')

@section('content')
@include('partials.page-header')
<section class="vacancy-archive">
    <div class="container">
        <header class="vacancy-archive-header">
            <h1 class="vacancy-archive-title h2">
                {{ __('Bekijk onze ', 'sage') }}<span class="vacancy-archive-title-ring">{{ __('openstaande vacatures', 'sage') }}</span>
            </h1>
            <p class="vacancy-archive-lead">{{ __('Kies een vacature voor meer informatie en om te solliciteren.', 'sage') }}</p>
        </header>
        @if(have_posts())
        <div class="vacancy-archive-grid">
            @while(have_posts())
            @php(the_post())
            @include('partials.loop-vacancy')
            @endwhile
        </div>
        @include('partials.pagination', ['query' => $GLOBALS['wp_query'] ?? null])
        @else
        <p class="vacancy-archive-empty">{{ __('Er zijn momenteel geen vacatures.', 'sage') }}</p>
        @endif
    </div>
</section>
@endsection

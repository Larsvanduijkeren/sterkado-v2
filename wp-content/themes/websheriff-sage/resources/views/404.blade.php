@extends('layouts.app')

@section('content')
@include('partials.page-header')

@if (! have_posts())
<section class="page-not-found">
    <div class="container">
        <div class="content" data-aos="fade-up">
            <h1 class="h2">Pagina niet gevonden</h1>
            <p>Deze pagina bestaat niet (meer). Met de onderstaande knop brengen we je terug naar de hoofdpagina.</p>
            <a href="/" class="btn">Naar home</a>
        </div>
    </div>
</section>
@endif
@endsection
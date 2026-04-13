@extends('layouts.app')

@section('content')
    @while(have_posts())
        @php(the_post())
        @includeFirst(['partials.single-' . get_post_type(), 'partials.single-default'])
    @endwhile
@endsection

@extends('layouts.app')

@section('content')
@include('partials.page-header')

@while(have_posts())
@php(the_post())
@includeFirst(['partials.loop-' . get_post_type(), 'partials.loop-default'])
@endwhile
@endsection
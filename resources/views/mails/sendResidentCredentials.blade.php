@extends('mails.layout-new')

@section('title')
    {{ $subject ?? "" }}
@endsection

@section('body')
    {!! $body ?? "" !!}
@endsection

@extends('mails.layout')

@section('title')
    {{__("New service request from resident")}}
@endsection

@section('body')
    <p>{{$resident->first_name}} {{$resident->last_name}} made a new service request.</p>
@endsection

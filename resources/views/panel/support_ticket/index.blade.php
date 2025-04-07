@extends('layouts.app')

@section('title', 'Support Tickets')

@section('content')

    <support-ticket :merchant_list="{{ $merchants }}"></support-ticket>

@endsection

@extends('layouts.app')

@section('title', 'Support Ticket Details')
@section('content')
    <support-ticket-details :uuid="{{ json_encode($uuid) }}"></support-ticket-details>
@endsection

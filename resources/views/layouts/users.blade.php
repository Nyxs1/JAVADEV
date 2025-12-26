@extends('layouts.app')

@section('content')
    {{-- Legacy layout - redirect to new unified dashboard --}}
    <script>window.location.href = "{{ route('users.dashboard', auth()->user()->username) }}";</script>
@endsection
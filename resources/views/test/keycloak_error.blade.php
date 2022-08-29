@extends('layout')
@section('header')
    <div class="ml-5">
        <h1>Test Microsoft OAuth</h1>
        {{ $error }}
    </div>
@endsection
@section('headerBgImageStyle')
@endsection
@section('content')
    <p></p>
    <div class="container">
        <header class="row">
            <h2>{{ $message }}</h2>
        </header>
        <div id="main" class="center">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">parameter</th>
                    <th scope="col">value</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($data as $key => $value)
                    <tr>
                        <td>{{ $key }}</td><td>{{ $value }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <footer class="row">
        </footer>
    </div>
@endsection

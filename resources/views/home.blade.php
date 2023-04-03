<?php $page = [
    'title' => 'My App',
    'component' => 'Example',
]; ?>
@extends('app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div id="home">
                    <Home></Home>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Ürünler')

@section('content')
<div id="app">
    <product-search 
    :initial-minprice='@json($minprice)'
    :initial-maxprice='@json($maxprice)'
    :initial-brands='@json($brands)'
    :initial-categories='@json($categories)'>
    /product-search>
</div>
@endsection
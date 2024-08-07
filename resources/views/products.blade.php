@extends('layouts.main')

@section('content')
    <div class="row justify-content-center gap-3">
        @foreach($products as $product)
            @php
                $imagePath = $product->images()->first()?->path;
            @endphp
            <div class="col-md-3 mb-4 d-flex justify-content-center">
                <div class="card" style="width: 350px;">
                    <img src="{{ $imagePath ? Storage::url($imagePath) : asset('images/no-image.jpg') }}"
                         class="card-img-top" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">{{ str($product->description)->limit() }}</p>
                        <a href="{{route('product.show', $product)}}" class="btn btn-primary">Подробнее</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

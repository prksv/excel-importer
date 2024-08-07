@extends('layouts.main')

<div style="position: absolute; top: 20px; left: 20px">
    <a class="btn btn-outline-primary" href="{{url('products')}}">Назад</a>
</div>

@section('content')
    <div class="row g-5">
        <div class="col-md-6">
            <div id="carousel" class="carousel slide">
                <div class="carousel-indicators">
                    @foreach($product->images as $key => $image)
                        <button type="button" data-bs-target="#carousel" data-bs-slide-to="{{$key}}"
                                class="{{ $key === 0 ? 'active' : '' }}"
                                aria-current="{{ $key === 0 ? 'true' : 'false' }}"
                                aria-label="Slide {{$key + 1}}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner">
                    @if($product->images->isEmpty())
                        <div class="carousel-item active">
                            <img src="{{asset('images/no-image.jpg')}}" class="d-block w-100"
                                 alt="No image">
                        </div>
                    @endif
                    @foreach($product->images as $key => $image)
                        <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                            <img src="{{ Storage::url($image->path) }}" class="d-block w-100"
                                 alt="Product Image {{$key + 1}}">
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        <div class="col-md-6">
            <div>
                <h1 class="display-5">{{ $product->name }}</h1>
                <p class="lead mt-5">{{ $product->description }}</p>
            </div>
            <hr/>
            <div>
                @foreach($product->additionalFields as $additionalField)
                    <p class="lead"><b>{{ $additionalField->key }}</b>: {{ $additionalField->value }}</p>
                @endforeach
            </div>
        </div>
    </div>
@endsection

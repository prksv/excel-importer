@extends('layouts.main')

@section('content')
    <h2 class="text-center mb-4">Импорт таблицы</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="uploadForm" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="form-group text-center">
                            <label for="fileInput" class="form-label d-block">Выберите файл</label>
                            <input type="file" class="form-control-file mx-auto" id="fileInput" name="file" required>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary">Загрузить</button>
                        </div>
                    </form>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">
                        Успешно!
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

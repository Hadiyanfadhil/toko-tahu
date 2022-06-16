@extends('admin.layouts.app')
@section('content')
<div class="row pt-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Color</li>
            </ol>
        </nav>
    </div>
</div>
<div class="row">
    <div class="col-4">
        <div class="card shadow-none">
            <div class="card-header">
                <h4 class="mb-0">Add Color</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin#createColor') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="">Name</label>
                        <input name="name" type="text" class="form-control" placeholder="enter brand name"
                            value="{{ old('name') }}">
                        @error('name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <button class="mt-3 btn btn-primary">Add Color</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-8">
        @include('admin.productColor.list')
    </div>
</div>
@endsection

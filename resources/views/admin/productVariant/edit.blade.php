@extends('admin.layouts.app')
@section('content')
<div class="row pt-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('admin#product')}}">Products</a></li>
              <li class="breadcrumb-item"><a href="{{ route('admin#product')}}">Product Variant</a></li>
              <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
          </nav>
    </div>
</div>
<div class="row pb-4">
    <div class="col-4">
        <div class="card shadow-none rounded">
            <div class="card-header">
                <h4 class="">Edit <span class="text-primary">{{ $product->name }}</span> Variants</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin#updateVariant',$variant->product_variants) }}" method="POST">
                    @csrf
                    <input type="hidden" name="productId" value="{{ $product->product_id }}">
                    <div class="form-group">
                        <label for="">Color</label>
                        <select name="colorId" class="form-control" id="">
                            <option value="">----- select colors ------</option>
                            @foreach ($colors as $item)
                                <option value="{{ $item->color_id }}" {{ old('colorId',$variant->color_id) == $item->color_id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('colorId')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="">Size</label>
                        <select name="sizeId" class="form-control" id="">
                            <option value="">----- select size ------</option>
                            @foreach ($sizes as $item)
                                <option value="{{ $item->size_id }}" {{ old('sizeId',$variant->size_id) == $item->size_id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('sizeId')
                                <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="">Stock</label>
                        <input name="avaiStock" type="number" class="form-control" placeholder="available stock" value="{{ old('avaiStock',$variant->available_stock) }}">
                        @error('avaiStock')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <button class="btn btn-primary shadow mt-3">Update Variant</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-8">
        @include('admin.productVariant.list')
    </div>
</div>
@endsection

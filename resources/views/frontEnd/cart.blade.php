@extends('frontEnd.layouts.app')
@section('content')
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a href="#">Home</a></li>
                          <li class="breadcrumb-item"><a href="#">Library</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Data</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="row min-vh-100">
                <div class="col-12">
                    <div class="card border-0">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">My Carts</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-primary text-nowrap text-white">
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th style="">Product</th>
                                            <th style="width:20%">Name</th>
                                            <th style="width:10%">Color</th>
                                            <th style="width:10%">Size</th>
                                            <th style="width:10%">Price</th>
                                            <th style="width:8%">Quantity</th>
                                            <th style="width:15%" class="text-center">Subtotal</th>
                                            <th style="">Remove</th>
                                        </tr>
                                    </thead>
                                    @if (Session::has('cart'))
                                        <tbody>
                                            @php
                                                $i = 1;
                                                $total = 0;
                                            @endphp
                                            @foreach (Session::get('cart') as $key => $item)
                                                @php
                                                    $total += $item['price'] * $item['quantity']
                                                @endphp
                                                <tr class="text-center">
                                                    <td>{{ $i++ }}</td>
                                                    <td>
                                                        <img src="{{ asset('uploads/products/'.$item['productImage']) }}" alt="" srcset="" style="width: 100px; heigth: 100px">
                                                    </td>
                                                    <td class="text-start">{{ $item['productName'] }}</td>
                                                    <td>{{ empty($item['color']) ? '.....' : $item['color'] }}</td>
                                                    <td>{{ empty($item['size']) ? '.....' : $item['size'] }}</td>
                                                    <td>{{ $item['price'] }} Ks</td>
                                                    <td>
                                                        <input type="number" id="{{ $key }}" class="qtyInput form-control" placeholder="qty" min="1" max="10" value="{{ $item['quantity'] }}" >
                                                    </td>
                                                    <td>{{ $item['price'] * $item['quantity'] }} Ks</td>
                                                    <td>
                                                        <a href="{{ route('frontend#deleteCart',$key) }}" class="btn btn-danger btn-sm text-white"><i class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>

                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="text-end">
                                                <td colspan="9">
                                                    <h5>Total : {{$total}} Ks</h5>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    @else
                                        <tbody>
                                            <tr class="text-center text-danger">
                                                <td colspan="9" class=" py-3">
                                                    There is No Carts
                                                </td>
                                            </tr>
                                        </tbody>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
<script>
    $('document').ready(function(){
        $('.qtyInput').on('change',function(){
            let quantity = $(this).val();
            let id = $(this).attr('id');
            $.ajax({
                url: "{{ route('frontend#updateCart') }}",
                method: "post",
                dataType: "json",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    quantity: quantity,
                },
                success:function(response){
                    window.location.reload();
                }

            })
        })
    })
</script>
@endsection

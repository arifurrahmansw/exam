@extends('layouts.app')
@php
    $query      = request()->get('title');
    $_variant   = request()->get('variant');
    $date       = request()->get('date');
    $min_price  = request()->get('price_from');
    $max_price  = request()->get('price_to');
@endphp
@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>
    <div class="card">
        <form action="" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control" value="{{ $query }}">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">
                        @if (!empty($variants) && count($variants) > 0)
                        @foreach ($variants as $variant)
                        {{-- <optgroup>{{ $variant->title }}</optgroup> --}}
                            <option value="{{ $variant->id }}">{{ $variant->title }}</option>
                            @if (!empty($variant->child))
                            @foreach ($variant->child as $child)
                            <option value="{{ $child->id }}" {{ $_variant=$child->id ? 'selected':'' }}> &nbsp; &nbsp; &nbsp;{{ $child->variant }}</option>
                            @endforeach
                            @endif
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control" value="{{ $min_price }}">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control" value="{{ $max_price }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control" value="{{ $date }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                        @if (!empty($data) && count($data) > 0)
                        @foreach ($data as $key=>$row)

                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $row->title }} <br>
                                Created at :
                                {{ date('d M Y', strtotime($row->created_at))}}
                            </td>
                            <td>
                                {{ Str::limit($row->description,40) }}
                            </td>
                            <td>
                                <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant{{ $row->id }}">

                                    @if (!empty($row->variants))
                                    @foreach ($row->variants as $variant)
                                    <dt class="col-sm-3 pb-0">
                                        @if ($variant->pv_2_variant)
                                            {{ $variant->pv_2_variant }}
                                        @endif
                                        @if ($variant->pv_1_variant)
                                        / {{ $variant->pv_1_variant }}
                                        @endif


                                        @if ($variant->pv_3_variant)
                                           / {{ $variant->pv_3_variant }}
                                        @endif

                                        {{-- SM/ Red/ V-Nick --}}
                                        {{-- SM/ Red/ V-Nick --}}
                                    </dt>
                                    <dd class="col-sm-9">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-4 pb-0">Price : {{ number_format($variant->product_variant_three,2) }}</dt>
                                            <dd class="col-sm-8 pb-0">InStock : {{ number_format($variant->stock,2) }}</dd>
                                        </dl>
                                    </dd>
                                    @endforeach
                                    @endif

                                </dl>
                                <button onclick="$('#variant{{ $row->id }}').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('product.edit', $row->id) }}" class="btn btn-success">Edit</a>
                                </div>
                            </td>
                        </tr>

                        @endforeach

                        @else

                        @endif




                    </tbody>

                </table>


            </div>

        </div>
        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} out of {{ $data->total() }}</p>
                </div>
                <div class="col-md-2">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

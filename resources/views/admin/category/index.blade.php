@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            <H4>類別清單 | Page</H4>
        </div>
        <div class="card-body">
            <table class="table  table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                    <tbody>
                        @foreach ($category as $item)
                            <tr>
                                <td> {{ $item->id }} </td>
                                <td> {{ $item->name }} </td>
                                <td> {{ $item->description }} </td>
                                <td> 
                                    <img src="{{ asset('assets/uploads/category/'.$item->image)}}" class="cate-image" alt="Image here"> 
                                </td>
                                <td> 
                                    <a href="{{ url('edit-category/'.$item->id) }}" class="btn btn-primary"> 編輯  </a>
                                    <a href="{{ url('delete-category/'.$item->id) }}" class="btn btn-danger"> 刪除 </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </thead>
            </table>
        </div>
@endsection
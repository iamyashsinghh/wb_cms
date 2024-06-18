@extends('layouts.app')

@section('title', 'Authors')

@section('main')
    <div class="content-wrapper pb-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Authors</h1>
                    </div>
                </div>
                <div class="button-group my-4">
                    <a href="{{ route('author.create') }}" class="btn btn-sm text-light buttons-print" style="background-color: var(--wb-renosand)">
                        <i class="fa fa-plus mr-1"></i>Add New
                    </a>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="table-responsive">
                    <table class="table text-sm">
                        <thead>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Bio</th>
                            <th class="text-center">Action</th>
                        </thead>
                        <tbody>
                            @foreach($authors as $author)
                                <tr>
                                    <td>{{ $author->id }}</td>
                                    <td>{{ $author->name }}</td>
                                    <td>
                                        @if($author->image)
                                            <img src="{{ $author->image }}" alt="{{ $author->name }}" style="width: 50px; height: 50px; object-fit: cover;">
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($author->bio, 50) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('author.edit', $author->id) }}" class="text-success mx-2" title="Edit">
                                            <i class="fa fa-edit" style="font-size: 15px;"></i>
                                        </a>
                                        <a href="{{ route('author.delete', $author->id) }}" class="text-danger mx-2" title="Delete" onclick="return confirm('Are you sure you want to delete this author?')">
                                            <i class="fa fa-trash-alt" style="font-size: 15px;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection

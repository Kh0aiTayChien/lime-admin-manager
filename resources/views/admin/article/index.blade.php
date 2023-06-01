@extends('layouts.admin')

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('List Words') }}</h1>

    @if (session('success'))
        <div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-left-danger" role="alert">
            <ul class="pl-4 my-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"></h6>
        </div>
        <div class="card-body">
            <div class="">
                <form method="GET" action="{{ route('articles.index',['conditionView' => $conditionView]) }}" class="row g-3 mb-4">
                    <div class="col-md-2">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Gõ từ khóa tìm kiếm vào"
                                   value="{{ request()->query('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <select class="form-select form-control" id="search_type" name="search_type">
                                <option value="title" {{ $searchType === 'title' ? 'selected' : '' }}>Tìm kiếm theo tiêu đề</option>
                                <option value="content" {{ $searchType === 'content' ? 'selected' : '' }}>Tìm kiếm theo nội dung</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">

                        <select class="form-select form-control " id="paginate" name="paginate">
                            <option value="1" {{ $paginate == 1 ? 'selected' : '' }}>1 bài</option>
                            <option value="2" {{ $paginate == 2 ? 'selected' : '' }}>2 bài</option>
                            <option value="5" {{ $paginate == 5 ? 'selected' : '' }}>5 bài</option>
                        </select>
                    </div>

                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"> Lọc </i></button>

                </form>

            </div>
            @include('admin/article/index/page',compact('articles','conditionView'))
        </div>
    </div>
@endsection

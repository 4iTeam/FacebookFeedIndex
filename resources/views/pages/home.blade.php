@extends('layouts.app')

@section('content')

    <div class="card-group mb-4">
        <div class="card">
            <div class="card-body" data-href="{{route('members')}}">
                <div class="h1 text-muted text-right mb-4">
                    <i class="icon-people"></i>
                </div>
                <div class="h4 mb-0">{{$members}}</div>
                <small class="text-muted text-uppercase font-weight-bold">Thành Viên</small>
            </div>
        </div>
        <div class="card">
            <div class="card-body" data-href="{{route('feed')}}">
                <div class="h1 text-muted text-right mb-4">
                    <i class="icon-grid"></i>
                </div>
                <div class="h4 mb-0">{{$posts}}</div>
                <small class="text-muted text-uppercase font-weight-bold">Bài viết</small>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="h1 text-muted text-right mb-4">
                    <i class="icon-bubble"></i>
                </div>
                <div class="h4 mb-0">{{$comments}}</div>
                <small class="text-muted text-uppercase font-weight-bold">Bình luận</small>

            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="h1 text-muted text-right mb-4">
                    <i class="icon-like"></i>
                </div>
                <div class="h4 mb-0">{{$reactions}}</div>
                <small class="text-muted text-uppercase font-weight-bold">Tương tác</small>

            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="h1 text-muted text-right mb-4">
                    <i class="icon-share"></i>
                </div>
                <div class="h4 mb-0">{{$shares}}</div>
                <small class="text-muted text-uppercase font-weight-bold">Lươt chia sẻ</small>

            </div>
        </div>
    </div>

    <div class="card card-accent-danger">
        <div class="card-header">
            Bài nổi bật
        </div>
        <div class="card-body">
            @include('pages.child.posts',['posts'=>$feed])
        </div>
    </div>
@endsection

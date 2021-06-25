@extends('layouts.app')
@section('content')
    @include('pages.child.search')

    @if(!empty($users) && count($users))
        <div class="row">
            @foreach($users as $user)
                <div class="col-lg-3 col-sm-6 col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="mr-3 float-left">
                                <div class="avatar avatar-lg">
                                    <img src="{{$user->avatar_lg}}" class="img-avatar">
                                </div>
                            </div>
                            <div>

                                <strong>{{$user->name}}</strong>
                                <div class="text-muted">
                                    <a title="Xem các bài viết của {{$user->name}}" href="{{route('member',[$user->slug])}}"><strong>{{$user->post_count}}</strong> bài viết</a>
                                    <div><strong>{{$user->comment_count}}</strong> bình luận</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach
        </div>
        <div class="row">
            <div class="col-sm-12 pull-right">
                {{$users->links()}}
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-sm-12">Không tìm thấy kết quả nào</div>
        </div>
    @endif
@endsection


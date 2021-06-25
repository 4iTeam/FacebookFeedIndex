@extends('layouts.app')
@section('content')
    @include('pages.child.search',['tags'=>null])


    @if($tags)
        <div class="card mb-1">
            <div class="card-body">
                @foreach($tags as $tag)
                    <a href="{{route('tag',$tag->tag)}}" class="badge badge-danger">{{$tag->tag}}({{$tag->post_count}})</a>
                @endforeach
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 pull-right">
                {{$tags->links()}}
            </div>
        </div>
    @endif

@endsection




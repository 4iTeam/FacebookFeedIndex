<div class="card mb-1">
    <div class="card-body">
        <form action="" method="GET">
            <div class="row">
                <div class="col-lg-12">
                    <div class="input-group">
                        <input type="text" name="q" value="{{request('q')}}" placeholder="Nhập từ khóa để tìm kiếm "
                               class="input form-control">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn btn-primary"> <i
                                        class="fa fa-search"></i> Search</button>
                        </span>
                    </div>
                </div>

            </div>
            @isset($tags)
                <div class="row">
                    <div class="col-lg-12">
                        @foreach($tags as $tag)
                            <a href="{{route('tag',$tag->tag)}}" class="badge badge-danger">{{$tag->tag}}({{$tag->post_count}})</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </form>
    </div>
</div>
@if($q=request('q'))
    @if(isset($filtered))
        <div class="row">
            <div class="col-sm-12">
                <div class="px-1">
                    <small class="text-muted">{!! $filtered !!} <a href="{{route('search').'?q='.$q}}">Xem toàn bộ kết quả</a></small>
                </div>
            </div>
        </div>
    @endif
@endif
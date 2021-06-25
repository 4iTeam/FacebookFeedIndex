@if($posts)
    @foreach($posts as $post)
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="feed-head">
                            <div class="mr-3 float-left">
                                <div class="avatar avatar-md">
                                    <a target="_blank" rel="nofollow noopener noreferrer" href="{{route('member',[$post->user->slug])}}"><img alt="{{$post->user->name}} avatar" data-src="{{$post->user->avatar}}" class="img-avatar lazyload"></a>
                                </div>
                            </div>
                            <div style="line-height: 16px">
                                <a href="{{route('member',[$post->user->slug])}}">
                                    <strong>{{$post->user->name}}</strong>
                                </a>
                                <div class="text-muted">
                                    Đã tạo <a class="text-success" target="_blank"
                                             rel="nofollow noopener noreferrer" href="{{$post->url}}"
                                              title="{{$post->created_at}}">
                                        <time class="time"
                                              datetime="{{$post->created_at}}">{{$post->created_at}}</time>
                                    </a><br>
                                    Cập nhật <a class="text-danger" target="_blank"
                                                  rel="nofollow noopener noreferrer" href="{{$post->url}}"
                                                  title="{{$post->updated_at}}">
                                        <time class="time"
                                              datetime="{{$post->updated_at}}">{{$post->updated_at}}</time>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="feed-body pt-3">
                            <p>{!! $post->content !!}</p>
                            @if($post->picture && !$post->isLink())
                                <div class="img-container">
                                    <img class="img-fluid lazyload" alt="post image" data-src="{{$post->picture}}">
                                </div>
                                <p></p>
                            @endif
                        </div>
                        <div class="feed-stats d-flex justify-content-start">
                            <span class="px-2" title="Tương tác"><i class="fa fa-thumbs-up"> </i> <a class="text-black" target="_blank"
                                                                                                     rel="nofollow noopener noreferrer" href="{{$post->url}}">{{$post->reaction_count}}</a></span>
                            <span class="px-2" title="Bình luận"><i class="fa fa-comments"> </i> <a class="text-black" target="_blank"
                                                                                                    rel="nofollow noopener noreferrer" href="{{$post->url}}">{{$post->comment_count}}</a></span>
                            <span class="px-2" title="Chia sẻ"><i class="fa fa-share"> </i> <a class="text-black" target="_blank"
                                                                                               rel="nofollow noopener noreferrer" href="{{$post->url}}">{{$post->share_count}}</a></span>
                        </div>
                        <hr>
                    </div>
                </div>
                <!--/.col-->
            </div>
        </div>
    @endforeach
    <!--/.row-->
    <div class="row">
        <div class="col-sm-12 pull-right">
            {{$posts->links()}}
        </div>
    </div>
@endif
@if(may_be_push('feed_scripts'))
    @push('scripts')
        <script src="{{asset('vendor/lazysizes/lazysizes.min.js')}}" async></script>
        <script src="{{asset('vendor/mmjs/mmjs.min.js')}}"></script>
        <script src="{{asset('vendor/mmjs/locate-vi.js')}}" charset="UTF-8"></script>
        <script>
            $('time').each(function () {
                var $this = $(this);
                var created_time = moment($this.attr('datetime'));
                var time;
                if (moment().diff(created_time, 'days') < 1) {
                    time = created_time.fromNow();
                } else {
                    time = created_time.calendar();
                }
                $this.html(time);
            })
        </script>
    @endpush
@endif

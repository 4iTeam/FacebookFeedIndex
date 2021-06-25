<!DOCTYPE html>
<html>
<head>
    <title>404 Not Found</title>

    <link href="{{asset('vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">

</head>
<body>
<div class="container">
    <div class="content">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h1>Server Error</h1>
            </div>
            <div class="panel-body">
                @isset($e)
                <div class="title">{{$e->getMessage()}}</div>
                @endif
                @if(url()->previous())
                    <a class="btn btn-info" href="{!! url()->previous() !!}">Go Back</a>
                @else
                    <a class="btn btn-info" href="{!! url('') !!}">Go Back</a>
                @endif
            </div>
        </div>

    </div>
</div>
</body>
</html>

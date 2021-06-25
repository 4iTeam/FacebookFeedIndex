@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-accent-success">
                <div class="card-header">
                    Bạn là thành viên của 4IT Community?
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-auto">
                            <a id="login" class="btn btn-success" href="{{url('login/facebook')}}">
                                <i class="fa fa-facebook" aria-hidden="true"></i> Vào thôi</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

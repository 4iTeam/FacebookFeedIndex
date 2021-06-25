@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h1>404 Not Found</h1>
                </div>
                <div class="card-body">
                    <div class="title">We couldn't find the page you looking for.</div>
                    @if(url()->previous())
                        <a class="btn btn-info btn-fill" href="{!! url()->previous() !!}">Go Back</a>
                    @else
                        <a class="btn btn-info btn-fill" href="{!! url('') !!}">Go Back</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

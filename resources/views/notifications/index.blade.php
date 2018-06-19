@extends('layouts.app')

@section('title')
    我的通知
@stop
@section('content')
    <div class="container">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                div.panel-body
            </div>
        </div>
    </div>
@if($notifications->count())

@else
    <div class="empty-block">没有消息通知！</div>
@endif
@stop
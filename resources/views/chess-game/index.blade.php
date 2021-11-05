@extends('includes.layout')
@section('body')
<div class="col-6 mx-auto mt-2">
    <div class="container-center">
        <div class="panel panel-filled">
            <div class="panel-content">
                <h4>List of chess games</h4>
                @foreach($chess_games as $game)
                    <div class="row">
                        <div class="col-2 text-right">
                            #{{ Arr::get($game, 'id') }}
                        </div>
                        <div class="col-9">
                            <a href="/">
                                {{ Arr::get($game, 'name') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.main')
@section('body')
    <div class="col-6 mx-auto mt-2">
        <div class="container-center">
            <div class="panel panel-filled">
                <div class="panel-content">
                    <div class="row">
                        <div class="col-lg-7">
                            <h5>List of chess games</h5>
                        </div>
                        <div class="col-lg-5">
                            <button id="create-game-button" type="button" class="btn btn-sm btn-dark">
                                <i class="fa fa-plus"></i>
                                Create game
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                        </div>
                        <div class="col-6">
                            Name
                        </div>
                        <div class="col-4">
                            Status
                        </div>
                    </div>
                    @foreach($chess_games as $i => $game)
                        <div class="row">
                            <div class="col-2 text-right">
                                #{{ 1 + $i + Arr::get($pagination, 'per_page') * (Arr::get($pagination, 'current_page') - 1) }}
                            </div>
                            <div class="col-6">
                                <a href="{{ route('chess-games.show', ['id' => Arr::get($game, 'id')]) }}">
                                    {{ Arr::get($game, 'name') }}
                                </a>
                            </div>
                            <div class="col-4">
                                {{ Arr::get($game, 'status') }}
                            </div>
                        </div>
                    @endforeach
                    <div class="row">
                        <div class="text-right">
                            @if (Arr::get($pagination, 'current_page') > 1)
                                <a href="{{ route('chess-games.index') }}?page={{ Arr::get($pagination, 'current_page') - 1 }}"
                                   class="btn btn-dark"
                                >
                                    <i class="fa fa-angle-left"></i>
                                </a>
                            @endif

                            @if (Arr::get($pagination, 'current_page') < Arr::get($pagination, 'last_page'))
                                <a href="{{ route('chess-games.index') }}?page={{ Arr::get($pagination, 'current_page') + 1}}"
                                   class="btn btn-dark"
                                >
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="create-game-form" method="POST" action="{{ route('chess-games.store') }}">
        @csrf
        <input type="hidden" name="name">
    </form>
    <script>
        $('#create-game-button').click(function () {
            let name = window.prompt('Enter game name:');

            $('#create-game-form input[name=name]').val(name);
            $('#create-game-form').submit();

            return false;
        });
    </script>
@endsection

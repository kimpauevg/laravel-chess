@php
$pieces = collect(Arr::get($chess_game, 'pieces', []));
@endphp
@extends('layouts.main')
@section('body')
    <style>
        .chess-board {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            grid-auto-rows: 1fr
        }
        .board-square {
            width: auto;
            height: 45px;
        }

        .background-dark {
            background: #6E6E6E;
        }
        .background-white {
            background: #bee5eb;
        }
    </style>
    <div class="col-8 mx-auto mt-2">
        <div class="container-center">
            <div class="chess-board">
                @for($vertical_coordinate = 8; $vertical_coordinate > 0; $vertical_coordinate--)
                    @for($horizontal_coordinate = 1; $horizontal_coordinate <= 8; $horizontal_coordinate++)
                        @php
                        $background = ($vertical_coordinate + $horizontal_coordinate) % 2 === 0 ? 'background-dark' : 'background-white';
                        $chess_piece = $pieces->where('coordinates.x', $horizontal_coordinate)->where('coordinates.y', $vertical_coordinate)->first();
                        @endphp
                        <div class="board-square {{ $background }}"
                             data-coordinate-x="{{ $horizontal_coordinate }}"
                             data-coordinate-y="{{ $vertical_coordinate }}"
                        >
                            @if ($chess_piece)
                                <img src="/images/pieces/{{ Arr::get($chess_piece, 'color') }}/{{Arr::get($chess_piece, 'chess_piece')}}.svg">
                            @endif
                        </div>
                    @endfor
                @endfor
            </div>
        </div>
    </div>
@endsection

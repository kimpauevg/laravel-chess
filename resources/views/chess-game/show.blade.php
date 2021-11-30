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

        .board-square-selected.background-dark {
            background: #c69500;
        }
        .board-square-selected.background-white {
            background: #d6a510;
        }

        .board-square-move.background-dark {
            background: #1e7e34;
        }
        .board-square-move.background-white {
            background: #2e8e44;
        }
    </style>
    <div class="col-lg-8 mx-auto mt-2">
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
                             @if ($chess_piece) data-chess-piece-id="{{ Arr::get($chess_piece, 'id') }}" @endif
                        >
                            @if ($chess_piece)
                                <img src="/images/pieces/{{ Arr::get($chess_piece, 'color') }}/{{Arr::get($chess_piece, 'name')}}.svg">
                            @endif
                        </div>
                    @endfor
                @endfor
            </div>
        </div>
    </div>
    <script>
        var selected_piece_id = null;

        $('.board-square').click(function () {
            let square = $(this);

            if (selected_piece_id === null) {
                selectPieceOnSquare(square);
                return;
            }

            if (selected_piece_id === getSquareChessPieceId(square)) {
                $('.chess-board .board-square')
                    .removeClass('board-square-selected')
                    .removeClass('board-square-move')

                selected_piece_id = null;
                return;
            }

            if (square.hasClass('board-square-move')) {
                let url = '{{ route('chess-games.move-chess-piece', ['id' => Arr::get($chess_game, 'id'), 'chess_piece_id' => '%piece_id%']) }}'
                    .replace('%piece_id%', selected_piece_id);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        coordinates: {
                            x: square.attr('data-coordinate-x'),
                            y: square.attr('data-coordinate-y'),
                        },
                    },
                    success: function () {
                        location.reload();
                    }
                })
            }
        });

        function getSquareChessPieceId(square) {
            return square.attr('data-chess-piece-id')
        }

        function selectPieceOnSquare(square) {
            let piece_id = getSquareChessPieceId(square);

            if (!piece_id) {
                return false;
            }

            selected_piece_id = piece_id;

            let url = '{{ route('chess-games.ajax.chess-piece-moves', ['id' => Arr::get($chess_game, 'id'), 'chess_piece_id' => '%piece_id%']) }}'
                .replace('%piece_id%', piece_id);

            $.ajax({
                url: url,
                success: function (data) {
                    square.addClass('board-square-selected');

                    for (let coordinates of data) {
                        let coordinate_selector = '[data-coordinate-x=' + coordinates.x + '][data-coordinate-y=' + coordinates.y + ']';
                        $('.board-square' + coordinate_selector).addClass('board-square-move');
                    }
                }
            });
        }
    </script>
@endsection

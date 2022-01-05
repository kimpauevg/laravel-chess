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
        .background-light {
            background: #bee5eb;
        }

        .board-square-selected.background-dark {
            background: #c69500;
        }
        .board-square-selected.background-light {
            background: #d6a510;
        }

        .board-square-move.background-dark {
            background: #1e7e34;
        }
        .board-square-move.background-light {
            background: #2e8e44;
        }

        .board-square-capture.background-dark {
            background: #990000;
        }
        .board-square-capture.background-light {
            background: #ce2029;
        }

        .board-square-castling.background-dark {
            background: #006dcf;
        }

        .board-square-castling.background-light {
            background: #005cbf;
        }

        .modal-content-dark {
            background: #2a2a2a;
        }
    </style>
    <div class="col-lg-8 mx-auto mt-2">
        <div class="container-center">
            <div class="chess-board">
                @for($vertical_coordinate = 8; $vertical_coordinate > 0; $vertical_coordinate--)
                    @for($horizontal_coordinate = 1; $horizontal_coordinate <= 8; $horizontal_coordinate++)
                        @php
                        $background = ($vertical_coordinate + $horizontal_coordinate) % 2 === 0 ? 'background-dark' : 'background-light';
                        $chess_piece = $pieces->where('coordinates.x', $horizontal_coordinate)->where('coordinates.y', $vertical_coordinate)->first();
                        @endphp
                        <div class="board-square {{ $background }}"
                             data-coordinate-x="{{ $horizontal_coordinate }}"
                             data-coordinate-y="{{ $vertical_coordinate }}"
                             @if ($chess_piece)
                             data-chess-piece-id="{{ Arr::get($chess_piece, 'id') }}"
                             data-chess-piece-color="{{ Arr::get($chess_piece, 'color') }}"
                             data-chess-piece-name="{{Arr::get($chess_piece, 'name')}}"
                             @endif
                        >
                        </div>
                    @endfor
                @endfor
            </div>
        </div>
    </div>
    <div class="modal fade" id="select-promotion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-content-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Select chess piece to promote to:</h5>
                </div>
                <div class="modal-body">
                    @foreach(Arr::get($dictionaries, 'promotable_chess_piece_names') as $name)
                        <button class="promote-pawn" data-name="{{ $name->name }}">
                            <img src="/images/pieces/{{ \App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary::DARK }}/{{$name->name}}.svg">
                            {{ $name->title }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script>
        var promotion_move_square = null;

        $('.board-square[data-chess-piece-id]').each(function () {
            let piece_name = $(this).attr('data-chess-piece-name');
            let color = $(this).attr('data-chess-piece-color');
            let link = getChessPieceLink(piece_name, color);
            let image = '<img src="' + link  + '">'
            $(this).append($(image));
        });

        $('.board-square').click(function () {
            let current_square = $(this);
            let selected_square  = getSelectedSquare();

            if (selected_square.length === 0) {
                selectPieceOnSquare(current_square);
                return;
            }

            if (current_square.hasClass('board-square-selected')) {
                $('.chess-board .board-square')
                    .removeClass('board-square-selected')
                    .removeClass('board-square-move')
                    .removeClass('board-square-capture')
                    .removeClass('board-square-castling')
                ;

                return;
            }

            if (current_square.hasClass('board-square-move')
                || current_square.hasClass('board-square-capture')
                || current_square.hasClass('board-square-castling')
            ) {
                let selected_piece_id = getSquareChessPieceId(selected_square);

                $.ajax({
                    url: getMakeMoveUrlForSquare(selected_piece_id),
                    method: 'POST',
                    data: getDataForSquare(current_square),
                    success: function () {
                        location.reload();
                    },
                    error: function (data) {
                        let errors = data.responseJSON.errors;

                        if (errors.promotion_to_piece_name === undefined) {
                            return;
                        }

                        let modal = $('#select-promotion');
                        modal.modal('show');

                        $('.promote-pawn').each(function () {
                            let piece_name = $(this).attr('data-name');
                            let color = getChessPieceSquareById(selected_piece_id).attr('data-chess-piece-color');
                            let image = '<img src="' + getChessPieceLink(piece_name, color) + '">';
                            $(this).html($(image));
                        });

                        promotion_move_square = current_square;
                    }
                });
            }
        });

        $('.promote-pawn').click(function () {
            let selected_square = getSelectedSquare();
            let data = getDataForSquare(promotion_move_square);
            data.promotion_to_piece_name = $(this).attr('data-name');

            $.ajax({
                url: getMakeMoveUrlForSquare(getSquareChessPieceId(selected_square)),
                method: 'POST',
                data: data,
                success: function () {
                    location.reload();
                },
            })
        });

        function getChessPieceSquareById(id) {
            return $('[data-chess-piece-id=' + id + ']');
        }

        function getMakeMoveUrlForSquare(piece_id) {
            return '{{ route('chess-games.move-chess-piece', ['id' => Arr::get($chess_game, 'id'), 'chess_piece_id' => '%piece_id%']) }}'
                .replace('%piece_id%', piece_id);
        }

        function getDataForSquare(square) {
            return {
                _token: '{{ csrf_token() }}',
                coordinates: {
                    x: square.attr('data-coordinate-x'),
                    y: square.attr('data-coordinate-y'),
                },
            };
        }

        function getSquareChessPieceId(square) {
            return square.attr('data-chess-piece-id');
        }

        function getSelectedSquare() {
            return $('.board-square-selected');
        }

        function getChessPieceLink(piece_name, color) {
            return '/images/pieces/' + color + '/' + piece_name + '.svg';
        }

        function selectPieceOnSquare(square) {
            let piece_id = getSquareChessPieceId(square);

            if (!piece_id) {
                return false;
            }

            let url = '{{ route('chess-games.ajax.chess-piece-moves', ['id' => Arr::get($chess_game, 'id'), 'chess_piece_id' => '%piece_id%']) }}'
                .replace('%piece_id%', piece_id);

            $.ajax({
                url: url,
                success: function (data) {
                    square.addClass('board-square-selected');

                    for (let coordinates of data.movements) {
                        getSelectorForCoordinates(coordinates).addClass('board-square-move');
                    }
                    for (let coordinates of data.captures.concat(data.en_passants)) {
                        getSelectorForCoordinates(coordinates).addClass('board-square-capture');
                    }

                    for (let coordinates of data.castlings) {
                        getSelectorForCoordinates(coordinates).addClass('board-square-castling')
                    }
                }
            });
        }

        function getSelectorForCoordinates(coordinates) {
            return $('.board-square[data-coordinate-x=' + coordinates.x + '][data-coordinate-y=' + coordinates.y + ']');
        }
    </script>
@endsection

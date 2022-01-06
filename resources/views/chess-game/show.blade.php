@extends('layouts.main')
@section('body')
    <div class="row justify-content-center">
        <div class="col-auto my-auto">
            <h4 class="mb-0">{{ Arr::get($chess_game, 'name') }}</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('chess-games.index') }}" class="btn btn-dark">
                <i class="fa fa-arrow-left"></i>
                To Menu
            </a>
        </div>
    </div>
    <div class="row mt-2 mx-auto justify-content-center">
        <div class="col-auto">
            <div class="chess-board float-right">
                @for($vertical_coordinate = 8; $vertical_coordinate > 0; $vertical_coordinate--)
                    @for($horizontal_coordinate = 1; $horizontal_coordinate <= 8; $horizontal_coordinate++)
                        @php
                            $background = ($vertical_coordinate + $horizontal_coordinate) % 2 === 0 ? 'background-dark' : 'background-light';
                        @endphp
                        <div class="board-square {{ $background }}"
                             data-coordinate-x="{{ $horizontal_coordinate }}"
                             data-coordinate-y="{{ $vertical_coordinate }}"
                        >
                        </div>
                    @endfor
                @endfor
            </div>
        </div>
        <div class="col-lg-3 ">
            <h5>Move history:</h5>
            <div class="move-history">
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
                        <button class="promote-pawn" data-name="{{ Arr::get($name, 'name') }}">
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="d-none">
    </div>
    <script>
        var promotion_move_square = null;

        reloadGameData();

        function reloadGameData() {
            $.ajax({
                url: '{{ route('chess-games.ajax.show', ['id' => Arr::get($chess_game, 'id')]) }}',
                dataType: 'JSON',
                success: function (data) {
                    emptyGameTable();

                    for (let piece of data.pieces) {
                        let coordinates_selector =
                            '[data-coordinate-x=' + piece.coordinates.x + ']' +
                            '[data-coordinate-y=' + piece.coordinates.y + ']';

                        let piece_square = $('.board-square' + coordinates_selector);
                        piece_square
                            .attr('data-chess-piece-id', piece.id)
                            .attr('data-chess-piece-name', piece.name)
                            .attr('data-chess-piece-color', piece.color)
                    }

                    for (let move of data.moves) {
                        $('.move-history').append('<div>' + move + '</div>')
                    }

                    renderPieces();
                },
            });
        }

        function renderPieces() {
            $('.board-square[data-chess-piece-id]').each(function () {
                let piece_name = $(this).attr('data-chess-piece-name');
                let color = $(this).attr('data-chess-piece-color');
                let link = getChessPieceLink(piece_name, color);
                let image = '<img src="' + link  + '">'
                $(this).append($(image));
            });
        }

        function emptyGameTable() {
            removePieceMoveSquares();
            $('.chess-board .board-square')
                .removeAttr('data-chess-piece-id')
                .removeAttr('data-chess-piece-name')
                .removeAttr('data-chess-piece-color')
                .html('');

            $('.move-history').html('');
        }

        function removePieceMoveSquares() {
            $('.chess-board .board-square')
                .removeClass('board-square-selected')
                .removeClass('board-square-move')
                .removeClass('board-square-capture')
                .removeClass('board-square-castling')
        }

        $('.board-square').click(function () {
            let current_square = $(this);
            let selected_square  = getSelectedSquare();

            if (selected_square.length === 0) {
                selectPieceOnSquare(current_square);
                return;
            }

            if (current_square.hasClass('board-square-selected')) {
                removePieceMoveSquares();
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
                        reloadGameData();
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

<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MakeMoveRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var ChessPieceDictionary $dictionary */
        $dictionary = app(ChessPieceDictionary::class);

        $names = $dictionary->names()->whereCanBePromotedTo()->getNames();

        return [
            'coordinates'             => ['required', 'array'],
            'coordinates.x'           => ['required', 'int', 'min:1', 'max:8'],
            'coordinates.y'           => ['required', 'int', 'min:1', 'max:8'],
            'promotion_to_piece_name' => ['string', Rule::in($names), 'nullable']
        ];
    }
}

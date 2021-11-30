<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MakeMoveRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'coordinates'   => ['required', 'array'],
            'coordinates.x' => ['required', 'int', 'min:1', 'max:8'],
            'coordinates.y' => ['required', 'int', 'min:1', 'max:8'],
        ];
    }
}

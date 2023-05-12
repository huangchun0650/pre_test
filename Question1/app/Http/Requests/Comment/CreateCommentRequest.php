<?php

namespace App\Http\Requests\Comment;

use App\Exceptions\Request\DuplicateNameException;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CreateCommentRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'post_id' => [
                "required",
                "integer",
                "min:1",
                Rule::exists('posts', 'id')
            ],
            'message' => "required|string|max:100",
        ];
    }
}

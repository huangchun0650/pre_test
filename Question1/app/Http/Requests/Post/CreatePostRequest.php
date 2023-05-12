<?php


namespace App\Http\Requests\Post;

use App\Exceptions\Request\DuplicateNameException;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;

class CreatePostRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'   => [
                "required",
                "string",
                "max:30",
                Rule::unique("posts"),
            ],
            'content' => "required|string|max:100",
        ];
    }

    public function messages()
    {
        return [
            'title.unique' => DuplicateNameException::class,
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;


class BaseRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $error = $validator->errors()->getMessageBag();

        if (class_exists($error->first())) {
            $key = Arr::first($error->keys());
            throw new ($error->first())($key);
        }

        return throw new ValidationException($validator);
    }
}

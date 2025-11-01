<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountOperationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(Request $request): array
    {
        if ($request->routeIs('deposit') || $request->routeIs('withdraw')) {
            return [
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric:strict|min:0.01',
                'comment' => 'string',
            ];
        }

        if ($request->routeIs('transfer')) {
            return [
                'from_user_id' => 'required|exists:users,id',
                'to_user_id' => 'required|different:from_user_id|exists:users,id',
                'amount' => 'required|numeric:strict|min:0.01',
                'comment' => 'string',
            ];
        }

        return [];
    }

    /**
     * {@inheritDoc}
     *
     * @see \Illuminate\Foundation\Http\FormRequest::failedValidation()
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Ошибка валидации! Проверьте поля.',
            'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY, [], JSON_UNESCAPED_UNICODE));
    }
}

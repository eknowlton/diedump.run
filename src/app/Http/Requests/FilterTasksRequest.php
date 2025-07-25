<?php

namespace App\Http\Requests;

use App\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterTasksRequest extends FormRequest 
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => [
                'nullable',
                Rule::in(['pending', 'completed', 'in-progress']),
            ],
            'status' =>[
                'nullable',
                'string',
                Rule::in(collect(TaskStatus::cases())->map(fn($status) => $status->value)),
            ],
            'search' => [
                'nullable',
                'string',
            ]
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use App\Models\Group;
use Illuminate\Foundation\Http\FormRequest;
use Closure;

class GroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard()->check();
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['string', 'max:255', 'unique:groups,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_department' => ['nullable', 'boolean'],
            'parent_id' => ['exists:groups,id', $this->validationAssignChildren()],
        ];

        if ($this->method() === 'POST') {
            $rules['name'][] = 'required';
        }

        return $rules;
    }

    public function validationAssignChildren(): Closure
    {
        return function ($attribute, $value, $fail) {
            $group = Group::find($value);
            $is_department = $this->is_department ?? false;

            if (!$group) {
                return true;
            }

            if ($is_department && $group->is_department) {
                $fail('Department cannot be assigned to a department.');
            }
        };
    }
}

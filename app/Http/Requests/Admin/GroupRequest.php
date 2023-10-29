<?php

namespace App\Http\Requests\Admin;

use App\Models\Group;
use Illuminate\Foundation\Http\FormRequest;
use Closure;
use Illuminate\Validation\Rule;

class GroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard()->check();
    }

    public function rules(): array
    {
        $rules = [
            'name' => [
                'string',
                'max:255',
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_department' => ['nullable', 'boolean'],
        ];

        if (!$this->is_department) {
            $rules['parent_id'] = [
                'nullable',
                'exists:groups,id',
                $this->validationAssignChildren(),
                $this->validationAssignItself(),
            ];
        }

        if ($this->method() === 'POST') {
            $rules['name'][] = 'required';
            $rules['name'][] = Rule::unique('groups', 'name')->where(function ($query) {
                $query->where('is_department', $this->is_department);
            });
        }

        return $rules;
    }

    public function validationAssignItself(): Closure
    {
        return function ($attribute, $value, $fail) {
            if ($this->route('group') &&
                $value == $this->route('group')->id) {
                $fail('The parent cannot be the current group.');
            }
        };
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

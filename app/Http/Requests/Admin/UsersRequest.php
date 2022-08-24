<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UsersRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $model = $this->route('user');
        $passwordRule = $model ? ['nullable'] : ['required'];

        $model = $this->route('user');
        return [
            'name' => ['bail', 'required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'email', 'max:255',  Rule::unique(User::class)->ignore($model->id ?? null)],
            'password' => ['bail', ...$passwordRule, Password::defaults()],
            'passwordConfirmation' => ['bail', ...$passwordRule, 'same:password'],
            'roleId' => ['bail', 'required', Rule::exists(Role::class, 'id')],
        ];
    }
}

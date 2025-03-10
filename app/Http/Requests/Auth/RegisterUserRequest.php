<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Rules\ValidCpf;

class RegisterUserRequest extends FormRequest
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
    public function rules(): array
    {
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'login' => ['required', 'string', 'max:255', 'unique:users,login'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'cpf' => ['required', 'string', new ValidCpf, 'unique:users,cpf'],
            'birth' => ['required', 'date'],
            'gender' => ['required', 'string'],
            'marital_status' => ['required', 'string'],
            'education_level' => ['nullable', 'string'],
            'salary' => ['nullable','numeric'],
            'courses' => ['array'],
            'courses.*.name' => ['required', 'string', 'max:255'],
            'courses.*.description' => ['required', 'string'],
            'courses.*.start_date' => ['required', 'date'],
            'courses.*.end_date' => ['nullable', 'date', 'after_or_equal:courses.*.start_date'],
            'experiences' => ['array'],
            'experiences.*.company' => ['required', 'string', 'max:255'],
            'experiences.*.position' => ['required', 'string', 'max:255'],
            'experiences.*.start_date' => ['required', 'date'],
            'experiences.*.end_date' => ['nullable', 'date', 'after_or_equal:experiences.*.start_date'],
            'experiences.*.is_current' => ['required', 'boolean'],
            'experiences.*.description' => ['required', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser uma string.',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres.',
            'email' => 'O campo :attribute deve ser um email válido.',
            'unique' => 'O campo :attribute já está em uso.',
            'confirmed' => 'A confirmação de :attribute não confere.',
            'date' => 'O campo :attribute deve ser uma data válida.',
            'numeric' => 'O campo :attribute deve ser um número.',
            'after_or_equal' => 'O campo :attribute deve ser uma data posterior ou igual a :date.',
        ];
    }
}

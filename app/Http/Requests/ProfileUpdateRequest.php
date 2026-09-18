<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            // Limite de 10240 KB (10MB)
            'avatar' => ['nullable'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Validações do Nome
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto válido.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',

            // Validações do E-mail
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso por outro usuário.',
            'email.max' => 'O e-mail não pode ter mais de 255 caracteres.',

            // Validações do Avatar
            'avatar.uploaded' => 'Falha ao carregar a foto de perfil. Verifique o tamanho do arquivo.',
            'avatar.image' => 'O arquivo enviado deve ser uma imagem.',
            'avatar.mimes' => 'A foto de perfil deve ser do tipo: JPG, JPEG, PNG ou WEBP.',
            'avatar.max' => 'A foto de perfil não pode ser maior que 10MB.',
        ];
    }
}
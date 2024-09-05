<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'statut' => 'required|integer|in:0,1,2',
        ];
    }

    public function messages()
    {
        return [
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut doit être l\'un des suivants : 0, 1, 2.',
            'image.required' => 'Une image est requise.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'Les formats de fichiers autorisés sont jpeg, png, jpg et gif.',
            'image.max' => 'La taille maximale de l\'image est de 2 Mo.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmploiDuTempsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // If it's a store request and 'actif' is not present, default to true
        // Otherwise, use the presence of the checkbox (for edit/update)
        if ($this->isMethod('post') && !$this->has('actif')) {
            $this->merge(['actif' => true]);
        } else {
            $this->merge([
                'actif' => $this->has('actif'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'jour' => 'nullable|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'professeur_id' => 'required|exists:professeurs,id,actif,1',
            'groupe_id' => 'required|exists:groupes,id',
            'module_id' => 'required|exists:modules,id',
            'type_seance' => 'required|in:Présentiel,Teams',
            'salle_id' => 'required_if:type_seance,Présentiel|nullable|exists:salles,id',
            'semaine_type' => 'nullable|in:Toutes,Paire,Impaire',
            'date_debut_validite' => 'required|date',
            'actif' => 'boolean'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $groupId = $this->input('groupe_id');
            $moduleId = $this->input('module_id');

            if ($groupId && $moduleId) {
                $groupe = \App\Models\Groupe::find($groupId);
                $module = \App\Models\Module::find($moduleId);

                if ($groupe && $module) {
                    $exists = \Illuminate\Support\Facades\DB::table('filiere_module')
                        ->where('filiere_id', $groupe->filiere_id)
                        ->where('module_id', $module->id)
                        ->exists();

                    if (!$exists) {
                        $validator->errors()->add('module_id', 'Ce module n\'est pas associé à la filière de ce groupe (' . $groupe->filiere->nom . ').');
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'jour.required' => 'Le jour est obligatoire',
            'heure_debut.required' => 'L\'heure de début est obligatoire',
            'heure_fin.required' => 'L\'heure de fin est obligatoire',
            'heure_fin.after' => 'L\'heure de fin doit être après l\'heure de début',
            'professeur_id.required' => 'Le professeur est obligatoire',
            'groupe_id.required' => 'Le groupe est obligatoire',
            'module_id.required' => 'Le module est obligatoire',
            'salle_id.required' => 'La salle est obligatoire',
        ];
    }
}

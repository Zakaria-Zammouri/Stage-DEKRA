<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait Loggable
{
    /**
     * Enregistre automatiquement les créations, modifications,
     * suppressions et restaurations de ce modèle dans le journal.
     */
    protected static function bootLoggable()
    {
        static::created(function ($model) {
            $model->enregistrerActivite('créé');
        });

        static::updated(function ($model) {

            $changements = array_keys($model->getChanges());

            // Si le seul changement est "deleted_at" qui redevient vide,
            // c'est une restauration, pas une vraie modification.
            // L'événement "restored" ci-dessous s'en charge déjà.
            $estUneRestauration = in_array('deleted_at', $changements)
                && count(array_diff($changements, ['deleted_at', 'updated_at'])) === 0;

            if ($estUneRestauration) {
                return;
            }

            $model->enregistrerActivite('modifié');
        });

        static::deleted(function ($model) {

            $forceDeleted = method_exists($model, 'isForceDeleting')
                && $model->isForceDeleting();

            $model->enregistrerActivite(
                $forceDeleted ? 'supprimé définitivement' : 'supprimé (corbeille)'
            );
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->enregistrerActivite('restauré');
            });
        }
    }

    /**
     * Écrit une ligne dans le journal des actions.
     */
    public function enregistrerActivite(string $action): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => class_basename($this),
            'model_id' => $this->id,
            'description' => $this->libelleActivite(),
        ]);
    }

    /**
     * Libellé lisible pour identifier l'élément concerné dans le journal.
     */
    public function libelleActivite(): string
    {
        foreach (['numero', 'titre', 'client', 'beneficiaire'] as $champ) {
            if (!empty($this->{$champ})) {
                return (string) $this->{$champ};
            }
        }

        return '#' . $this->id;
    }
}
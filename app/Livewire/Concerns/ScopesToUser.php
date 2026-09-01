<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Regra de visibilidade do CRM: admin e manager enxergam os dados de toda a
 * equipe, vendedores (sales) apenas os registros que pertencem a eles.
 */
trait ScopesToUser
{
    protected function currentUser(): User
    {
        return auth()->user();
    }

    /**
     * @param  class-string<Model>  $model
     */
    protected function scoped(string $model): Builder
    {
        $user = $this->currentUser();

        return $model::query()
            ->when($user->isSales(), fn (Builder $query) => $query->where('user_id', $user->id));
    }

    /**
     * Aborta com 403 quando um vendedor tenta acessar registro de outra pessoa.
     */
    protected function authorizeOwnership(Model $record): void
    {
        if ($this->currentUser()->isSales() && $record->user_id !== $this->currentUser()->id) {
            abort(403);
        }
    }
}

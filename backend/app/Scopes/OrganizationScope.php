<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class OrganizationScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $user = Auth::user();

        if (! $user) {
            return;
        }

        if ($user->role === 'admin' && empty($user->organization_id)) {
            return;
        }

        if (! empty($user->organization_id)) {
            $builder->where($model->getTable() . '.organization_id', $user->organization_id);
        }
    }
}

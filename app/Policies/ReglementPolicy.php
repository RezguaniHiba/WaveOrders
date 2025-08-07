<?php

namespace App\Policies;

use App\Models\Reglement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReglementPolicy
{
    /**
     * Determine whether the user can view any models.
     */
public function viewAny(User $user, Commande $commande): bool
    {
return $user->role === 'admin' ||
           $commande->commercial_id === $user->id ||
           $commande->cree_par === $user->id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Reglement $reglement): bool
    {
        return $user->role === 'admin'
            || $reglement->commande->commercial_id === $user->id
            || $reglement->commande->cree_par === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Reglement $reglement): bool
    {
        return $this->view($user, $reglement);
    }

    public function delete(User $user, Reglement $reglement): bool
    {
        return $this->view($user, $reglement);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Reglement $reglement): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Reglement $reglement): bool
    {
        return false;
    }
}

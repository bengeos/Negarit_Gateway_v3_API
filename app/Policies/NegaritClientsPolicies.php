<?php

namespace App\Policies;

use App\Models\NegaritClient;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NegaritClientsPolicies
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->role_id == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can view any negarit clients.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the negarit client.
     *
     * @param User $user
     * @param NegaritClient $negaritClient
     * @return mixed
     */
    public function view(User $user, NegaritClient $negaritClient)
    {
        return true;
    }

    /**
     * Determine whether the user can create negarit clients.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the negarit client.
     *
     * @param User $user
     * @param NegaritClient $negaritClient
     * @return mixed
     */
    public function update(User $user, NegaritClient $negaritClient)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the negarit client.
     *
     * @param User $user
     * @param NegaritClient $negaritClient
     * @return mixed
     */
    public function delete(User $user, NegaritClient $negaritClient)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the negarit client.
     *
     * @param User $user
     * @param NegaritClient $negaritClient
     * @return mixed
     */
    public function restore(User $user, NegaritClient $negaritClient)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the negarit client.
     *
     * @param User $user
     * @param NegaritClient $negaritClient
     * @return mixed
     */
    public function forceDelete(User $user, NegaritClient $negaritClient)
    {
        return true;
    }
}

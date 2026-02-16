<?php

namespace App\Policies;

use App\Models\User as TargetUser;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:User');
    }

    public function view(AuthUser $authUser, TargetUser $user): bool
    {
        return $authUser->can('View:User');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:User');
    }

    public function update(AuthUser $authUser, TargetUser $user): bool
    {
        // Managers cannot update super-admin profiles
        if ($this->isManager($authUser) && $this->isSuperAdmin($user)) {
            return false;
        }

        return $authUser->can('Update:User');
    }

    public function delete(AuthUser $authUser, TargetUser $user): bool
    {
        // Managers cannot delete super-admin profiles
        if ($this->isManager($authUser) && $this->isSuperAdmin($user)) {
            return false;
        }

        return $authUser->can('Delete:User');
    }

    public function restore(AuthUser $authUser, TargetUser $user): bool
    {
        // Managers cannot restore super-admin profiles (consistency)
        if ($this->isManager($authUser) && $this->isSuperAdmin($user)) {
            return false;
        }
        return $authUser->can('Restore:User');
    }

    public function forceDelete(AuthUser $authUser, TargetUser $user): bool
    {
        // Managers cannot force-delete super-admin profiles
        if ($this->isManager($authUser) && $this->isSuperAdmin($user)) {
            return false;
        }
        return $authUser->can('ForceDelete:User');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:User');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:User');
    }

    public function replicate(AuthUser $authUser, TargetUser $user): bool
    {
        // Managers cannot replicate super-admin profiles
        if ($this->isManager($authUser) && $this->isSuperAdmin($user)) {
            return false;
        }
        return $authUser->can('Replicate:User');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:User');
    }

    private function isManager(AuthUser $user): bool
    {
        return method_exists($user, 'hasRole') && $user->hasRole('organization-manager');
    }

    private function isSuperAdmin(TargetUser $user): bool
    {
        return method_exists($user, 'hasRole') && ($user->hasRole('super-admin') || $user->hasRole('super_admin'));
    }
}
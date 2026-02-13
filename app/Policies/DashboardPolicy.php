<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Dashboard;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Dashboard');
    }

    public function view(AuthUser $authUser, Dashboard $dashboard): bool
    {
        return $authUser->can('View:Dashboard');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Dashboard');
    }

    public function update(AuthUser $authUser, Dashboard $dashboard): bool
    {
        return $authUser->can('Update:Dashboard');
    }

    public function delete(AuthUser $authUser, Dashboard $dashboard): bool
    {
        return $authUser->can('Delete:Dashboard');
    }

    public function restore(AuthUser $authUser, Dashboard $dashboard): bool
    {
        return $authUser->can('Restore:Dashboard');
    }

    public function forceDelete(AuthUser $authUser, Dashboard $dashboard): bool
    {
        return $authUser->can('ForceDelete:Dashboard');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Dashboard');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Dashboard');
    }

    public function replicate(AuthUser $authUser, Dashboard $dashboard): bool
    {
        return $authUser->can('Replicate:Dashboard');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Dashboard');
    }

}
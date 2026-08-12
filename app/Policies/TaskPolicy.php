<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function update(User $user, Task $task): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        // Ensure tenant isolation: user and task must belong to same company
        if ($user->company_id !== $task->company_id) {
            return false;
        }

        // Allow if user created or was assigned the task
        return $user->id === ($task->assigned_by ?? $task->created_by) || $user->id === ($task->created_by ?? null);
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->company_id !== $task->company_id) {
            return false;
        }

        return $user->id === ($task->assigned_by ?? $task->created_by) || $user->id === ($task->created_by ?? null);
    }
}


<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Boletin;
use Illuminate\Auth\Access\Response;

class BoletinPolicy
{
    /**
     * Optional: A 'before' method to bypass all authorization checks for a super-admin role.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('administrador')) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any models (for the list).
     * Corresponds to 'ver boletines' permission.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver boletines') || $user->hasRole('operador');
    }

    /**
     * Determine whether the user can view the model.
     * Corresponds to 'ver boletines' permission for a specific boletin.
     * Un creador puede ver sus propios boletines; un operador puede ver boletines pendientes.
     */
    public function view(User $user, Boletin $boletin): bool
    {
        return $user->can('ver boletines') || $user->id === $boletin->user_id || ($user->hasRole('operador') && $boletin->estado === 'pendiente');
    }

    /**
     * Determine whether the user can create models.
     * Corresponds to 'crear boletines' permission.
     */
    public function create(User $user): bool
    {
        return $user->can('crear boletines');
    }

    /**
     * Determine whether the user can update the model.
     * Corresponds to 'editar boletines' permission.
     * Los creadores pueden editar sus boletines si están pendientes o rechazados.
     */
    public function update(User $user, Boletin $boletin): bool
    {
        return $user->can('editar boletines') && $user->id === $boletin->user_id &&
            ($boletin->estado === 'pendiente' || $boletin->estado === 'rechazado');
    }

    /**
     * Determine whether the user can delete the model.
     * Corresponds to 'eliminar boletines' permission.
     * Solo los creadores pueden eliminar sus boletines y solo si están pendientes o rechazados.
     */
    public function delete(User $user, Boletin $boletin): bool
    {
        return $user->can('eliminar boletines') && $user->id === $boletin->user_id &&
            ($boletin->estado === 'pendiente' || $boletin->estado === 'rechazado');
    }

    /**
     * Determine whether the user can import boletines (PDFs).
     * Corresponds to 'importar boletines' permission.
     */
    public function import(User $user): bool
    {
        return $user->can('importar boletines');
    }

    /**
     * Determine whether the user can export boletines (CSV).
     * Corresponds to 'exportar boletines' permission.
     */
    public function export(User $user): bool
    {
        return $user->can('exportar boletines');
    }

    /**
     * Determine whether the user can validate/approve a boletin.
     * Corresponds to 'validar boletines' permission and role 'operador'.
     */
    public function validar(User $user, Boletin $boletin): bool
    {
        // Solo un operador con el permiso 'validar boletines' y el boletín debe estar pendiente.
        return $user->hasRole('operador') && $user->can('validar boletines') && $boletin->estado === 'pendiente';
    }

    /**
     * Determine whether the user can reject a boletin.
     * Corresponds to 'rechazar boletines' permission and role 'operador'.
     */
    public function rechazar(User $user, Boletin $boletin): bool
    {
        // Solo un operador con el permiso 'rechazar boletines' y el boletín debe estar pendiente.
        return $user->hasRole('operador') && $user->can('rechazar boletines') && $boletin->estado === 'pendiente';
    }
}

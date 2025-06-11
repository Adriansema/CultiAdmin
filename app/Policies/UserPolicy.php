<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        // Debug: Indicar que se ha alcanzado el método before
        /* dd('Reached UserPolicy::before method for ability: ' . $ability . ' for user ' . $user->email); */

        // Si el usuario es administrador...
        if ($user->hasRole('administrador')) {
            // Debug: Indicar que el usuario es administrador
            /* dd('User ' . $user->email . ' is administrador in before method for ability: ' . $ability); */

            // Si la habilidad está en la lista de delegación, se devuelve null
            if (in_array($ability, ['toggle', 'delete', 'manageRolesAndPermissions'])) {
                // Debug: Delegando habilidad específica para el administrador
                /* dd('Delegating specific ability ' . $ability . ' for administrator ' . $user->email); */
                return null; // Delega a los métodos específicos de la política
            }
            // Debug: Administrador tiene acceso total para otras habilidades
            /* dd('Administrator ' . $user->email . ' has full access for ability: ' . $ability); */
            return true; // El administrador tiene permiso para todas las demás habilidades
        }

        // Debug: No administrador, delegando a métodos específicos
        /* dd('User ' . $user->email . ' is NOT administrator, delegating to specific methods for ability: ' . $ability); */
        return null; // Para usuarios no administradores, siempre delega a los métodos específicos
    }

    /**
     * Determine whether the authenticated user can view any users (for the list).
     * Corresponds to 'ver lista de usuarios' permission.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver lista de usuarios');
    }

    /**
     * Determine whether the authenticated user can view a specific user.
     * Corresponds to 'ver lista de usuarios' permission.
     */
    public function view(User $user, User $model): bool
    {
        // Un usuario siempre puede ver su propio perfil.
        if ($user->id === $model->id) {
            return true;
        }

        // Si no es su propio perfil, necesita el permiso general para ver usuarios.
        return $user->can('ver lista de usuarios');
    }

    /**
     * Determine whether the authenticated user can create users.
     * Corresponds to 'crear usuarios' permission.
     */
    public function create(User $user): bool
    {
        return $user->can('crear usuarios');
    }

    /**
     * Determine whether the authenticated user can update a user's basic profile.
     * Corresponds to 'editar usuarios' permission.
     */
    public function update(User $user, User $model): bool
    {
        // Un administrador puede editar a cualquier usuario, incluso a otros administradores.
        // Si el usuario logueado es administrador, permite la edición.
        if ($user->hasRole('administrador')) {
            return true;
        }

        // Un usuario no administrador no puede editar a un administrador.
        if ($model->hasRole('administrador')) {
            return false;
        }

        // Un usuario puede editar su propio perfil, o si tiene el permiso 'editar usuarios'.
        return $user->id === $model->id || $user->can('editar usuarios');
    }

    /**
     * Determine whether the authenticated user can toggle the status of a user.
     * Corresponds to 'activar usuarios' or 'desactivar usuarios' permission.
     */
    public function toggle(User $user, User $model): bool
    {
        // El dd() es de depuración, ya no es necesario en producción.
        /* dd('Reached UserPolicy::toggle method'); */

        // Un usuario no puede activar/desactivar su propio estado.
        if ($user->id === $model->id) {
            return false;
        }

        // Un usuario no administrador no puede activar/desactivar a un administrador.
        if ($model->hasRole('administrador') && !$user->hasRole('administrador')) {
            return false;
        }

        // Se requiere el permiso 'activar usuarios' o 'desactivar usuarios'.
        return $user->can('activar usuarios') || $user->can('desactivar usuarios');
    }

    /**
     * Determine whether the authenticated user can delete a user.
     * Corresponds to 'eliminar usuarios' permission.
     */
    public function delete(User $user, User $model): bool
    {
        // Un usuario no puede eliminarse a sí mismo.
        if ($user->id === $model->id) {
            return false;
        }

        // Un usuario no administrador no puede eliminar a un administrador.
        if ($model->hasRole('administrador') && !$user->hasRole('administrador')) {
            return false;
        }

        // Se requiere el permiso 'eliminar usuarios'.
        return $user->can('eliminar usuarios');
    }

    /**
     * Determine whether the authenticated user can import users.
     * Corresponds to 'importar usuarios' permission.
     */
    public function import(User $user): bool
    {
        return $user->can('importar usuarios');
    }

    /**
     * Determine whether the authenticated user can export users.
     * Corresponds to 'exportar usuarios' permission.
     */
    public function export(User $user): bool
    {
        return $user->can('exportar usuarios');
    }

    /**
     * Determine whether the authenticated user can manage (update) roles and direct permissions of a user.
     * This is a specific check for the update method related to roles/permissions UI section and backend saving.
     * Corresponds to 'editar roles y permisos de usuario' permission.
     */
    public function manageRolesAndPermissions(User $user, User $model): bool
    {
       // Debug: Indicar que se ha alcanzado el método manageRolesAndPermissions
        /* dd('Reached UserPolicy::manageRolesAndPermissions method for user ' . $user->email . ' editing ' . $model->email); */

        // 1. Un administrador (el $user logueado) SIEMPRE puede modificar roles/permisos de CUALQUIER usuario,
        //    incluido él mismo. Esta es la prioridad.
        if ($user->hasRole('administrador')) {
            // Debug: Administrador PASA manageRolesAndPermissions
            /* dd('Admin ' . $user->email . ' PASSED manageRolesAndPermissions for ' . $model->email); */
            return true;
        }

        // 2. Si el usuario logueado NO es administrador, entonces:
        //    No puede modificar roles/permisos de OTRO administrador.
        if ($model->hasRole('administrador')) {
            // Debug: No-admin intenta editar admin, FALLA manageRolesAndPermissions
            /* dd('Non-admin ' . $user->email . ' FAILED manageRolesAndPermissions (editing admin ' . $model->email . ')'); */
            return false;
        }

        // 3. Si el usuario logueado NO es administrador, NO puede modificar sus PROPIOS roles/permisos.
        //    Esto previene que un no-administrador se auto-promocione o se deshabilite accidentalmente.
        if ($user->id === $model->id) {
            // Debug: No-admin intenta auto-editar, FALLA manageRolesAndPermissions
            /* dd('Non-admin ' . $user->email . ' FAILED manageRolesAndPermissions (self-editing)'); */
            return false;
        }

        // 4. Para cualquier otro caso (un no-administrador editando a otro no-administrador),
        //    se requiere el permiso específico 'editar roles y permisos de usuario'.
        $result = $user->can('editar roles y permisos de usuario');
        // Debug: Resultado final para no-admin editando a otro no-admin
        dd('Non-admin ' . $user->email . ' result for manageRolesAndPermissions: ' . ($result ? 'TRUE' : 'FALSE'));
        return $result;
    }

    /**
     * Determine whether the authenticated user can generate CSV of dummy users.
     * Corresponds to 'generar_usuarios_prueba' permission.
     */
    public function generateCsv(User $user): bool
    {
        return $user->can('generar_usuarios_prueba');
    }
}

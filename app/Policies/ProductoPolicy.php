<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Producto;
use Illuminate\Auth\Access\Response;

class ProductoPolicy
{
    /**
     * Optional: A 'before' method to bypass all authorization checks for a super-admin role.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Si 'administrador' tiene acceso total a todos los recursos, no necesita los permisos específicos.
        if ($user->hasRole('administrador')) {
            return true;
        }

        return null; // Continuar con las comprobaciones normales
    }

    /**
     * Determine whether the user can view any models (for the list).
     * Corresponds to 'ver productos' permission.
     */
    public function viewAny(User $user): bool
    {
        // Cualquier usuario que pueda ver productos (creadores o operadores)
        return $user->can('ver productos') || $user->hasRole('operador');
    }

    /**
     * Determine whether the user can view the model.
     * Corresponds to 'ver productos' permission for a specific product.
     * Un creador puede ver sus propios productos; un operador puede ver productos pendientes.
     */
    public function view(User $user, Producto $producto): bool
    {
        // Creador puede ver sus propios productos, Operador puede ver si el estado es 'pendiente' o tiene permiso general
        return $user->can('ver productos') || $user->id === $producto->user_id || ($user->hasRole('operador') && $producto->estado === 'pendiente');
    }

    /**
     * Determine whether the user can create models.
     * Corresponds to 'crear productos' permission.
     */
    public function create(User $user): bool
    {
        return $user->can('crear productos');
    }

    /**
     * Determine whether the user can update the model.
     * Corresponds to 'editar productos' permission.
     * Los creadores pueden editar sus productos si están pendientes o rechazados.
     * Los operadores no editan el contenido, solo aprueban/rechazan.
     */
    public function update(User $user, Producto $producto): bool
    {
        // Un usuario puede editar su propio producto si tiene el permiso
        // Y el producto está en estado 'pendiente' o 'rechazado' (para permitir correcciones).
        return $user->can('editar productos') && $user->id === $producto->user_id &&
            ($producto->estado === 'pendiente' || $producto->estado === 'rechazado');
    }

    /**
     * Determine whether the user can delete the model.
     * Corresponds to 'eliminar productos' permission.
     * Solo los creadores pueden eliminar sus productos y solo si están pendientes o rechazados.
     */
    public function delete(User $user, Producto $producto): bool
    {
        // Creador puede eliminar su producto si tiene permiso
        return $user->can('eliminar productos') && $user->id === $producto->user_id &&
            ($producto->estado === 'pendiente' || $producto->estado === 'rechazado');
    }

    /**
     * Determine whether the user can import products.
     * Corresponds to 'importar productos' permission.
     */
    public function import(User $user): bool
    {
        // El usuario necesita el permiso 'importar productos'
        return $user->can('importar productos');
    }

    /**
     * Determine whether the user can export products.
     * Corresponds to 'exportar productos' permission.
     */
    public function export(User $user): bool
    {
        return $user->can('exportar productos');
    }

    /**
     * Determine whether the user can validate/approve a product.
     * Corresponds to 'validar productos' permission and role 'operador'.
     */
    public function validar(User $user, Producto $producto): bool
    {
        // Solo un operador con el permiso 'validar productos' y el producto debe estar pendiente.
        return $user->hasRole('operador') && $user->can('validar productos') && $producto->estado === 'pendiente';
    }

    /**
     * Determine whether the user can reject a product.
     * Corresponds to 'rechazar productos' permission and role 'operador'.
     */
    public function rechazar(User $user, Producto $producto): bool
    {
        // Solo un operador con el permiso 'rechazar productos' y el producto debe estar pendiente.
        return $user->hasRole('operador') && $user->can('rechazar productos') && $producto->estado === 'pendiente';
    }
}

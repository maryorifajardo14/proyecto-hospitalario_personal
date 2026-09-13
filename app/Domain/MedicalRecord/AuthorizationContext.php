<?php

namespace App\Domain\MedicalRecord;

/**
 * DTO puro que representa "quien" solicita abrir o consultar un expediente,
 * ya resuelto por la capa de aplicacion a partir del usuario autenticado
 * (JWT + roles Spatie). El dominio nunca toca el modelo Eloquent User.
 */
final class AuthorizationContext
{
    /**
     * @param  string[]  $roles
     */
    public function __construct(
        public readonly int $userId,
        public readonly array $roles,
    ) {
    }

    public function hasAnyRole(array $allowedRoles): bool
    {
        foreach ($this->roles as $role) {
            if (in_array($role, $allowedRoles, true)) {
                return true;
            }
        }

        return false;
    }
}

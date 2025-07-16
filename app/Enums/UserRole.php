<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case AdminMagasin = 'admin_magasin';
    case Employe = 'employe';

    /**
     * Obtenir le libellé français du rôle
     */
    public function label(): string
    {
        return match($this) {
            self::SuperAdmin => 'Super Administrateur',
            self::AdminMagasin => 'Administrateur Magasin',
            self::Employe => 'Employé',
        };
    }

    /**
     * Vérifier si le rôle a des permissions d'administration
     */
    public function isAdmin(): bool
    {
        return in_array($this, [self::SuperAdmin, self::AdminMagasin]);
    }

    /**
     * Vérifier si c'est un super administrateur
     */
    public function isSuperAdmin(): bool
    {
        return $this === self::SuperAdmin;
    }

    /**
     * Obtenir tous les rôles disponibles
     */
    public static function all(): array
    {
        return [
            self::SuperAdmin,
            self::AdminMagasin,
            self::Employe,
        ];
    }
}

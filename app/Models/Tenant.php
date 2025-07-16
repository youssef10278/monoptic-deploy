<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'subscription_end_date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subscription_end_date' => 'datetime',
    ];

    /**
     * Relation avec les utilisateurs du tenant
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relation avec les clients du tenant
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Relation avec les catégories de produits du tenant
     */
    public function productCategories(): HasMany
    {
        return $this->hasMany(ProductCategory::class);
    }

    /**
     * Relation avec les produits du tenant
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relation avec les ventes du tenant
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Relation avec les paiements du tenant
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Relation avec les prescriptions du tenant
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * Vérifier si le tenant est actif
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Vérifier si le tenant est en période d'essai
     */
    public function isTrial(): bool
    {
        return $this->status === 'trial';
    }

    /**
     * Vérifier si l'abonnement du tenant a expiré
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' ||
               ($this->subscription_end_date && $this->subscription_end_date->isPast());
    }
}

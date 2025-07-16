<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sale_id',
        'amount',
        'payment_method',
        'tenant_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec la vente
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Relation avec le tenant (magasin)
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relation avec l'utilisateur qui a enregistré le paiement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour filtrer par tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Constantes pour les méthodes de paiement
     */
    const PAYMENT_METHODS = [
        'cash' => 'Espèces',
        'card' => 'Carte bancaire',
        'transfer' => 'Virement',
        'check' => 'Chèque',
        'other' => 'Autre'
    ];

    /**
     * Obtenir le libellé de la méthode de paiement
     */
    public function getPaymentMethodLabelAttribute()
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }
}

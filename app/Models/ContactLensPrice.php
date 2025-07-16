<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactLensPrice extends Model
{
    protected $fillable = [
        'tenant_id',
        'lens_type',
        'brand',
        'duration',
        'box_size',
        'base_price',
        'complexity_multiplier',
        'description',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'complexity_multiplier' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec le tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope pour filtrer par tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope pour les prix actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculer le prix final en fonction des paramètres
     */
    public function calculatePrice($parameters = [])
    {
        $finalPrice = $this->base_price;

        // Appliquer le multiplicateur de complexité
        $finalPrice *= $this->complexity_multiplier;

        // Multiplicateurs selon les paramètres techniques
        if (isset($parameters['sphere']) && abs($parameters['sphere']) > 6.0) {
            $finalPrice *= 1.2; // +20% pour forte correction
        }

        if (isset($parameters['cylinder']) && abs($parameters['cylinder']) > 2.0) {
            $finalPrice *= 1.15; // +15% pour fort astigmatisme
        }

        if (isset($parameters['addition']) && $parameters['addition'] > 0) {
            $finalPrice *= 1.1; // +10% pour multifocales
        }

        return round($finalPrice, 2);
    }

    /**
     * Constantes pour les types de lentilles
     */
    const TYPE_SPHERIQUE = 'spherique';
    const TYPE_TORIQUE = 'torique';
    const TYPE_MULTIFOCALE = 'multifocale';
    const TYPE_COSMETIQUE = 'cosmétique';
    const TYPE_RIGIDE = 'rigide';
    const TYPE_SCLERALE = 'sclerale';

    const TYPES = [
        self::TYPE_SPHERIQUE => 'Sphérique',
        self::TYPE_TORIQUE => 'Torique',
        self::TYPE_MULTIFOCALE => 'Multifocale',
        self::TYPE_COSMETIQUE => 'Cosmétique',
        self::TYPE_RIGIDE => 'Rigide',
        self::TYPE_SCLERALE => 'Sclérale'
    ];

    /**
     * Constantes pour les durées
     */
    const DURATION_DAILY = 'journaliere';
    const DURATION_WEEKLY = 'hebdomadaire';
    const DURATION_MONTHLY = 'mensuelle';
    const DURATION_QUARTERLY = 'trimestrielle';
    const DURATION_YEARLY = 'annuelle';

    const DURATIONS = [
        self::DURATION_DAILY => 'Journalière',
        self::DURATION_WEEKLY => 'Hebdomadaire',
        self::DURATION_MONTHLY => 'Mensuelle',
        self::DURATION_QUARTERLY => 'Trimestrielle',
        self::DURATION_YEARLY => 'Annuelle'
    ];
}

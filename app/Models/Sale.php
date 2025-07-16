<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'total_amount',
        'client_id', // Nullable - peut être null pour les ventes anonymes
        'user_id',
        'tenant_id',
        'status',
        'paid_amount',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec le client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation avec l'utilisateur (employé qui a fait la vente)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le tenant (magasin)
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relation avec les lignes de vente
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Scope pour filtrer par tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope pour filtrer par type de vente
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour les dossiers lunettes
     */
    public function scopeDossierLunettes($query)
    {
        return $query->where('type', self::TYPE_DOSSIER_LUNETTES);
    }

    /**
     * Scope pour les ventes directes
     */
    public function scopeVenteDirecte($query)
    {
        return $query->where('type', self::TYPE_VENTE_DIRECTE);
    }

    /**
     * Relation avec les paiements
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Calculer le nombre total d'articles vendus
     */
    public function getTotalItemsAttribute()
    {
        return $this->saleItems->sum('quantity');
    }

    /**
     * Calculer le montant restant à payer
     */
    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Vérifier si la vente est entièrement payée
     */
    public function getIsFullyPaidAttribute()
    {
        return $this->paid_amount >= $this->total_amount;
    }

    /**
     * Vérifier si la vente est partiellement payée
     */
    public function getIsPartiallyPaidAttribute()
    {
        return $this->paid_amount > 0 && $this->paid_amount < $this->total_amount;
    }

    /**
     * Constantes pour les types de vente
     */
    const TYPE_DOSSIER_LUNETTES = 'dossier_lunettes';
    const TYPE_VENTE_DIRECTE = 'vente_directe';

    const TYPES = [
        self::TYPE_DOSSIER_LUNETTES => 'Dossier Lunettes',
        self::TYPE_VENTE_DIRECTE => 'Vente Directe'
    ];

    /**
     * Constantes pour les statuts (nouveau cycle de vie)
     */
    const STATUS_DEVIS = 'devis';
    const STATUS_EN_COMMANDE = 'en_commande';
    const STATUS_PRET_POUR_LIVRAISON = 'pret_pour_livraison';
    const STATUS_LIVRE = 'livre';
    const STATUS_ANNULE = 'annule';

    const STATUSES = [
        self::STATUS_DEVIS => 'Devis',
        self::STATUS_EN_COMMANDE => 'En commande',
        self::STATUS_PRET_POUR_LIVRAISON => 'Prêt pour livraison',
        self::STATUS_LIVRE => 'Livré',
        self::STATUS_ANNULE => 'Annulé'
    ];

    /**
     * Obtenir le libellé du statut
     */
    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Mettre à jour le statut de paiement
     */
    public function updatePaymentStatus()
    {
        // Pour les ventes, on ne change pas le statut principal basé sur le paiement
        // Le statut principal suit le cycle de vie du produit (devis -> commande -> livraison)
        // Le statut de paiement est géré séparément via paid_amount

        // Optionnel: Si la vente est entièrement payée et en statut devis, on peut la passer en commande
        if ($this->paid_amount >= $this->total_amount && $this->status === self::STATUS_DEVIS) {
            $this->status = self::STATUS_EN_COMMANDE;
        }

        $this->save();
    }

    /**
     * Relation avec l'historique des statuts
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(SaleStatusHistory::class)->orderBy('changed_at', 'desc');
    }
}

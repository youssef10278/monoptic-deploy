<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'brand',
        'reference',
        'purchase_price',
        'selling_price',
        'quantity',
        'barcode',
        'product_category_id',
        'tenant_id',
        'type',
        'attributes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'quantity' => 'integer',
        'attributes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec le tenant (magasin)
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relation avec la catégorie de produit
     */
    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    /**
     * Scope pour filtrer par tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope pour filtrer par catégorie
     */
    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('product_category_id', $categoryId);
    }

    /**
     * Scope pour filtrer par type de produit
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour les montures
     */
    public function scopeFrames($query)
    {
        return $query->where('type', 'frame');
    }

    /**
     * Scope pour les lentilles de contact
     */
    public function scopeContactLenses($query)
    {
        return $query->where('type', 'contact_lenses');
    }

    /**
     * Scope pour les accessoires
     */
    public function scopeAccessories($query)
    {
        return $query->where('type', 'accessory');
    }

    /**
     * Calculer la marge bénéficiaire
     */
    public function getMarginAttribute()
    {
        if (!$this->purchase_price || !$this->selling_price) {
            return null;
        }

        return $this->selling_price - $this->purchase_price;
    }

    /**
     * Calculer le pourcentage de marge
     */
    public function getMarginPercentageAttribute()
    {
        if (!$this->purchase_price || !$this->selling_price || $this->purchase_price == 0) {
            return null;
        }

        return (($this->selling_price - $this->purchase_price) / $this->purchase_price) * 100;
    }
}

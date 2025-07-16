<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'price',
        'description',
        'details',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'details' => 'array',
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
     * Relation avec le produit
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculer le sous-total de cette ligne
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->price;
    }

    /**
     * Vérifier si l'item est un produit en stock
     */
    public function getIsStockProductAttribute()
    {
        return !is_null($this->product_id);
    }

    /**
     * Vérifier si l'item est un produit sur mesure
     */
    public function getIsCustomProductAttribute()
    {
        return is_null($this->product_id) && !empty($this->description);
    }

    /**
     * Obtenir le nom de l'item (produit ou description)
     */
    public function getItemNameAttribute()
    {
        if ($this->product) {
            return $this->product->name;
        }

        return $this->description ?? 'Article sans nom';
    }
}

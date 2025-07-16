<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleStatusHistory extends Model
{
    protected $fillable = [
        'sale_id',
        'old_status',
        'new_status',
        'comment',
        'user_id',
        'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime'
    ];

    /**
     * Relation avec la vente
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Relation avec l'utilisateur qui a fait le changement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

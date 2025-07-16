<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'tenant_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
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
     * Relation avec les ventes du client
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }



    /**
     * Scope pour filtrer par tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Accesseur pour first_name (compatibilité)
     */
    public function getFirstNameAttribute()
    {
        $nameParts = explode(' ', $this->name, 2);
        return $nameParts[0] ?? '';
    }

    /**
     * Accesseur pour last_name (compatibilité)
     */
    public function getLastNameAttribute()
    {
        $nameParts = explode(' ', $this->name, 2);
        return $nameParts[1] ?? '';
    }

    /**
     * Mutateur pour first_name (compatibilité)
     */
    public function setFirstNameAttribute($value)
    {
        $lastName = $this->last_name ?? '';
        $this->attributes['name'] = trim($value . ' ' . $lastName);
    }

    /**
     * Mutateur pour last_name (compatibilité)
     */
    public function setLastNameAttribute($value)
    {
        $firstName = $this->first_name ?? '';
        $this->attributes['name'] = trim($firstName . ' ' . $value);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContactLensBrand extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'normalized_name',
        'usage_count',
        'last_used_at'
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'usage_count' => 'integer'
    ];

    /**
     * Relation avec le tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Normalise le nom de la marque pour la recherche
     */
    public static function normalizeName($name)
    {
        return Str::lower(Str::ascii($name));
    }

    /**
     * Trouve ou crée une marque
     */
    public static function findOrCreate($tenantId, $brandName)
    {
        $normalizedName = self::normalizeName($brandName);

        $brand = self::where('tenant_id', $tenantId)
                    ->where('normalized_name', $normalizedName)
                    ->first();

        if ($brand) {
            // Incrémenter l'usage et mettre à jour la dernière utilisation
            $brand->increment('usage_count');
            $brand->update(['last_used_at' => now()]);
        } else {
            // Créer une nouvelle marque
            $brand = self::create([
                'tenant_id' => $tenantId,
                'name' => $brandName,
                'normalized_name' => $normalizedName,
                'usage_count' => 1,
                'last_used_at' => now()
            ]);
        }

        return $brand;
    }

    /**
     * Recherche de marques avec autocomplétion
     */
    public static function searchBrands($tenantId, $query, $limit = 10)
    {
        $normalizedQuery = self::normalizeName($query);

        return self::where('tenant_id', $tenantId)
                  ->where('normalized_name', 'like', $normalizedQuery . '%')
                  ->orderBy('usage_count', 'desc')
                  ->orderBy('last_used_at', 'desc')
                  ->limit($limit)
                  ->pluck('name')
                  ->toArray();
    }
}

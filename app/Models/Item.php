<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Item extends Model
{
    //
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_barang', 'stok_barang', 'harga_barang', 'selling_price', 'is_asset', 'min_stock'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'nama_barang',
        'kode_barang',
        'harga_barang',
        'selling_price',
        'wholesale_price',
        'promo_price',
        'promo_start_date',
        'promo_end_date',
        'stok_barang',
        'category_id',
        'unit_id',
        'supplier_id',
        'min_stock',
        'is_asset',
        'purchase_date',
        'useful_life_months',
        'salvage_value'
    ];

    protected $casts = [
        'is_asset' => 'boolean',
        'purchase_date' => 'date',
        'promo_start_date' => 'date',
        'promo_end_date' => 'date',
        'harga_barang' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'salvage_value' => 'decimal:2',
    ];

    /**
     * Get effective selling price based on customer type and promotions.
     */
    public function getEffectivePrice($customer = null)
    {
        // 1. Check for active promotion
        if ($this->promo_price && $this->promo_start_date <= now() && $this->promo_end_date >= now()) {
            return $this->promo_price;
        }

        // 2. Check for customer-specific pricing
        if ($customer) {
            if ($customer->category?->name === 'Wholesale' && $this->wholesale_price) {
                return $this->wholesale_price;
            }
            
            // Apply category discount percentage if any
            if ($customer->category?->discount_percent > 0) {
                return $this->selling_price * (1 - ($customer->category->discount_percent / 100));
            }
        }

        return $this->selling_price ?: $this->harga_barang;
    }

    public function getProfitMarginAttribute()
    {
        if (!$this->selling_price || $this->selling_price <= 0 || !$this->harga_barang) return 0;
        return (($this->selling_price - $this->harga_barang) / $this->selling_price) * 100;
    }

    public function getProfitPotentialAttribute()
    {
        if (!$this->selling_price || !$this->harga_barang) return 0;
        return ($this->selling_price - $this->harga_barang) * $this->stok_barang;
    }

    public function getCurrentValueAttribute()
    {
        if (!$this->is_asset || !$this->purchase_date || !$this->useful_life_months) return $this->harga_barang;

        $monthsPassed = $this->purchase_date->diffInMonths(now());
        if ($monthsPassed >= $this->useful_life_months) return $this->salvage_value ?? 0;

        $totalDepreciation = $this->harga_barang - ($this->salvage_value ?? 0);
        $monthlyDepreciation = $totalDepreciation / $this->useful_life_months;
        
        return $this->harga_barang - ($monthlyDepreciation * $monthsPassed);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class)->withPivot('stock')->withTimestamps();
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function reconciliations()
    {
        return $this->hasMany(Reconciliation::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }

}

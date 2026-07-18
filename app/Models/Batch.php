<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Batch extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'item_id',
        'warehouse_id',
        'batch_number',
        'quantity',
        'expiry_date'
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['batch_number', 'quantity', 'expiry_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}

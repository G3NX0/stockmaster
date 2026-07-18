<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Reconciliation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'item_id',
        'user_id',
        'system_stock',
        'physical_stock',
        'difference',
        'reason',
        'status'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['physical_stock', 'difference', 'reason'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

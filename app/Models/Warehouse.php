<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Warehouse extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'location'])
            ->logOnlyDirty();
    }

    protected $fillable = ['name', 'location'];

    public function items()
    {
        return $this->belongsToMany(Item::class)->withPivot('stock')->withTimestamps();
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }
}

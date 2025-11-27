<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lapangan extends Model
{
    use HasFactory;

    protected $table = 'lapangans';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'location',
        'photo',
        'description',
        'price_per_hour',
        'status',
    ];

    // Generate UUID otomatis
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function reservations()
    {
        return $this->hasMany(Reservasi::class);
    }
}

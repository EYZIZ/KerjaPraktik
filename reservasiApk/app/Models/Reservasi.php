<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Reservasi extends Model
{
    use HasFactory;

    protected $table = 'reservasis';

    // Karena kolom id pakai UUID
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'lapangan_id',
        'tanggal',
        'jam_mulai',
        'durasi',
        'jam_selesai',
        'total_harga',
        'status',
        'payment_status',
        'payment_method',
        'payment_transaction_id',
        'paid_at',
    ];

    // Generate UUID otomatis saat create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relasi ke User (user_id = integer)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Lapangan (lapangan_id = uuid)
    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class);
    }
}

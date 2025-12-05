<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservasiSlot extends Model
{
    protected $table = 'reservasi_slots';

    protected $fillable = [
        'reservasi_id',
        'lapangan_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
    ];

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class);
    }

    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class);
    }
}

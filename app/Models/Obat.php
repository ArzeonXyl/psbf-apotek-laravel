<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Obat extends Model
{
    use HasFactory;

    protected $table = 'obat'; // Nama tabel eksplisit
    protected $primaryKey = 'ID_OBAT'; // Primary key eksplisit

    protected $fillable = [
        'NAMA_OBAT',
        'KATEGORI',
        'KETERANGAN',
        'JUMLAH_STOCK',
        'HARGA',
        'EXP',
        'ID_SUPPLIER',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'HARGA' => 'decimal:2',
        'EXP' => 'date',
        'JUMLAH_STOCK' => 'integer',
    ];

    /**
     * Mendapatkan supplier untuk obat ini.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'ID_SUPPLIER', 'ID_SUPPLIER');
    }
}
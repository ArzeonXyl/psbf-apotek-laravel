<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier'; // Nama tabel eksplisit
    protected $primaryKey = 'ID_SUPPLIER'; // Primary key eksplisit

    protected $fillable = [
        'NAMA_SUPPLIER',
        'ALAMAT_SUPPLIER',
        'TELEPON_SUPPLIER',
    ];

    /**
     * Mendapatkan semua obat yang disuplai oleh supplier ini.
     */
    public function obats(): HasMany // Nama method jamak dari model Obat
    {
        return $this->hasMany(Obat::class, 'ID_SUPPLIER', 'ID_SUPPLIER');
    }
}
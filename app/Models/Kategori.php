<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;
    protected $table = 'm_kategori';
    protected $primaryKey = 'kategori_id';
    protected $guarded = [];
    public function barang()
    {
        return $this->hasMany(Barang::class);
    }
}

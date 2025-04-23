<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;
    protected $table = 't_penjualan';
    protected $primaryKey = 'penjualan_id';
    protected $guarded = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
    public function detail_penjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id');
    }
}

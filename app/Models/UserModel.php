<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UserModel extends Authenticatable implements JWTSubject
{
    public function getJWTIdentifier(){
        return $this->getKey();
    }
    public function getJWTCustomClaims(){
        return [];
    }

    use HasFactory;

    protected $table = 'm_user';
    protected $primaryKey = 'user_id';
    protected $fillable = ['level_id', 'username', 'nama', 'password', 'created_at', 'updated_at', 'image'];
    protected $hidden = ['password'];
    protected $casts = ['password' => 'hashed'];

    public function level()
    {
        return $this->belongsTo(LevelModel::class, 'level_id');
    }

    public function image():Attribute
    {
        return Attribute::make(
            get: fn($image)=> url('/storage/posts/'.$image),
        );
    }

    public function getRoleName(): string{
        return $this->level->level_nama;
    }

    public function getRole(){
        return $this->level->level_kode;
    }

    public function hasRole($role): bool
    {
        return $this->getRole() == $role;
    }
}

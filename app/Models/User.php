<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject; // Tambahkan ini

class User extends Authenticatable implements JWTSubject // Implementasikan JWTSubject
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password']; // Tambahkan ini

    /**
     * Menentukan ID yang akan digunakan dalam JWT
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Menentukan klaim tambahan yang akan disertakan dalam JWT
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
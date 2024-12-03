<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens;

    protected $fillable = [
        'username',
        'company_name',
        'phoneno',
        'address',
        'company_documents',
        'company_logo',
        'reffered_by',
        'role',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    
    public function bankdetail()
    {
        return $this->hasMany(BankDetail::class);
    }
    
    public function wallet()
    {
        return $this->hasMany(Wallet::class);
    }
}



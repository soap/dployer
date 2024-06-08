<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function ownedBy(): User
    {
        return User::where('email', $this->email)->first();
    }
}

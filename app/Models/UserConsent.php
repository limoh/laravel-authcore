<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserConsent extends Model
{
    protected $fillable = ['user_id', 'client_id', 'scopes', 'granted_at'];
    protected $casts = ['scopes' => 'array', 'granted_at' => 'datetime'];
}

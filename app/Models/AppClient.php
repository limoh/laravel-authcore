<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppClient extends Model
{
    protected $fillable = [
        'name', 'client_id', 'client_secret', 'redirect_uris', 'allowed_scopes', 'trusted'
    ];
}

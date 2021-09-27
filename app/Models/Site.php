<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'taboola_api_client_id',
        'taboola_api_client_secret',
        'taboola_api_account_id',
        'google_analytics_api_view_id',
    ];
}

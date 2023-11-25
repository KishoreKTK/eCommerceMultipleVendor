<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreferredCategory extends Model
{
    use HasFactory;
    protected $fillables = ['user_id','category_id'];
}

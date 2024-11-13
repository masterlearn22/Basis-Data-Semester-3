<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class users extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'iduser';
    public $timestamps = true; // created_at & updated_at

    protected $fillable = [
        'username',
        'password',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    public function School()
    {
        return $this->belongsTo(School::class);
    }

    public function Programs(){
        return $this->hasMany(Program::class);
    }
    protected $fillable = [
        'name','school_id'
    ];
    
}

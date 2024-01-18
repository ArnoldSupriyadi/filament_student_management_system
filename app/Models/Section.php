<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'name'
    ];

    public function claases()
    {
        return $this->belongsTo(Classes::class);
    }

    public function student()
    {
        return $this->hasMany(Student::class);
    }
}

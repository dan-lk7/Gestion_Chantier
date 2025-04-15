<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chantier extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'adresse', 'pays','responsable_id',];

    // 🔹 Relation avec les utilisateurs
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // 🔹 Relation avec les stocks
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    // 🔹 Relation avec les mouvements de stock
    public function movements()
    {
        return $this->hasManyThrough(Movement::class, Stock::class);
    }
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}

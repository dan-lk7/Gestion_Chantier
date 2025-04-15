<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use HasFactory;

    protected $fillable = ['stock_id', 'user_id', 'type', 'quantite', 'date','chantier_source_id', 'destination_chantier_id'];

    public static function getTypes()
    {
        return ['entrée', 'sortie', 'transfert'];
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function chantierSource()
    {
        return $this->belongsTo(Chantier::class, 'chantier_source_id');
    }

    public function chantierDestination()
    {
        return $this->belongsTo(Chantier::class, 'destination_chantier_id');
    }
}

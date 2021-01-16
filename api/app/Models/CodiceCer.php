<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CodiceCer extends Model
{
	protected $table = "codici_cer";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cdcer',		// codice numerico
		'dscer',		// descrizione breve
		'txcer'			// descrizione lunga
    ];
    /**
     * The attributes that are excluded from the model's JSON form.
     *
     * @var array
     */
   protected $hidden = [
		"created_at",
		"updated_at",
	];
	
}
<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Impianto extends Model
{
	protected $table = "impianti";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		"codice",
		"tipo",
		"cat",
		"lotto",
		"comune",
		"impianto",
		"ae_progetto",
		"limiti",
		"stato",
		"num",
		"data",
		"autotizzazione",
		"accessibilita",
		"superficie",
		"nord",
		"est",
		"latitude",
		"longitude",
		"tecnico_responsabile",
		"squadra",
		"responsabile"	
    ];
	
	public static $rules = array(
		'impianto' => 'required',
		'codice' => 'required|codice|unique:impianti',
	);
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
		"created_at",
		"updated_at",
	];
	

}
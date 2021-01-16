<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Lookup extends Model
{
	protected $table = 'Lookup';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag', 
		'info1', 
		'info2', 
		'info3', 
		'info4', 
    ];
	
	public static $rules = array(
		'tag' => 'required',
		'info1' => 'required',
	);
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
		
	];
	

}
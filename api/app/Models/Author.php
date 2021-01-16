<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use HasFactory;

class Author extends Model
{
    /**
     * Get the books that belong to this Author
     */
    public function books()
    {
        return $this->hasMany('\App\Models\Book');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'name', 			
		'email', 			
		'streetAddress',	
		'city',			
		'phoneNumber',		
		'company', 		
		'catchPhrase',		
		'freeText',		
		'dt'	,				
    ];
	
	public static $rules = array(
		'name' => 'required',
		'email' => 'required|email|unique:authors',
	);
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
		
	];
	

}
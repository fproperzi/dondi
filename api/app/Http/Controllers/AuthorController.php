<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthorController extends Controller
{

    public function showAll()
    {
        return response()->json(Author::all());
    }

    public function showOne($id)
    {
        return response()->json(Author::find($id));
    }

    public function create(Request $request)
    {
		$this->validate($request, Author::$rules);
		
/* 		$twitter = $request->input( 'twitter' );	
		$twitter = isset($twitter) ? '' : app('hash')->make($twitter);
		$request->merge([
			'twitter' => $twitter,
			'github'=>'12345user'
		]);
 */
        $author = Author::create($request->all());

        return response()->json($author, 201);
    }

    public function update($id, Request $request)
    {
		$author = Author::findOrFail($id);
		//$request->merge(['github'=>'12345user']);
		$data = $request->all();
		//if(isset($data['twitter'])) $data['twitter'] = app('hash')->make($data['twitter']);
		$author->update($data);
		return response()->json($author, 200);
    }

    public function delete($id)
    {
        Author::findOrFail($id)->delete();
        return response('Deleted Successfully', 200);
    }
	//------------------------------------------------------------------
	public function showAllBooks()
    {
        $books = Book::all();
        return response()->json($books, 200);
    }
    public function showOneBook($author_id, $book_id)
    {
        $author = Author::find($author_id);
        $book = $author->books
                       ->where('id', '=', $book_id)
                       ->first();
        return response()->json($book, 200);
    }
	public function showAllBooksFromAuthor($author_id)
	{
		try {
			$author = Author::findOrFail($author_id);
		} catch(ModelNotFoundException $e) {
			return response('Author not found', 404);
		}
			$books = $author->books;
			return response()->json($books, 200);
	}
	
	public function createBook($author_id, Request $request)
    {
        $author = Author::find($author_id);
        $book = Book::create([
            'title' => $request->title,
            'author_id' => $author->id
        ]);
        return response()->json($book, 201);
    }
	public function updateBook($author_id, $book_id, Request $request)
    {
         $author = Author::find($author_id);
         $book = $author->books
                       ->where('id', '=', $book_id)
                       ->first()
                       ->update($request->all());
					   
		$updatedBook = $author->books
                              ->where('id', '=', $book_id)
                              ->first();
         return response()->json($updatedBook, 200);			   
         //return response()->json($book, 200);
    }
    public function deleteBook($author_id, $book_id)
    {
        $author = Author::find($author_id);
        $book = $author->books
                       ->where('id', '=', $book_id)
                       ->first()
                       ->delete();
        return response('Deleted Successfully', 200);
    }
	
	
	
}
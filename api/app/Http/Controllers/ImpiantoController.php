<?php

namespace App\Http\Controllers;

use App\Models\Impianto;
use App\Models\Intervento;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class ImpiantoController extends Controller
{

    public function showAll()
    {
        return response()->json(Impianto::all());
    }

    public function showOne($id)
    {
        return response()->json(Impianto::find($id));
    }

    public function create(Request $request)
    {
		$this->validate($request, Impianto::$rules);
        $impianto = Impianto::create($request->all());

        return response()->json($impianto, 201);
    }

    public function update($id, Request $request)
    {
		$impianto = Impianto::findOrFail($id);
		$data = $request->all();
		$impianto->update($data);
		
		return response()->json($impianto, 200);
    }

    public function delete($id)
    {
        Impianto::findOrFail($id)->delete();
		
        return response('Deleted Successfully', 200);
    }
	//------------------------------------------------------------------

	//--- impianti x tipo
	public function NuImpiantixTipo() {
        $rs = Impianto::select('tipo',DB::raw('count(*) as cnt'))
						->groupBy('tipo')
						->orderBy('tipo')->get();
						
		return response()->json($rs);
    }
	public function ImpiantixTipo($tipo_id) {
        $rs = Impianto::select()
						->where('tipo','=',$tipo_id)
						->orderBy('comune')->orderBy('impianto')->get();
						
		return response()->json($rs);
    }
	
	//-- impianti x tipo x comune
	public function NuImpiantixComune() {
        $rs = [];
		$rs['title'] = 'Comuni';
		$rs['comuni'] = Impianto::select('comune',DB::raw('count(*) as cnt'))
						->groupBy('comune')
						->orderBy('comune')->get();
						
		return response()->json($rs);
    }
	public function ImpiantixComune($comune_id) {
        $rs = Impianto::select()
						->where('comune','=',urldecode($comune_id))
						->orderBy('tipo')->get();
						
		return response()->json($rs);
    }
	public function TipiImpiantoxComune($comune_id) {
		$rs = []; 
		$rs['title'] = urldecode($comune_id);
        $rs['tipi'] = Impianto::select('tipo','comune',DB::raw('count(*) as cnt'))
						->where('comune','=',urldecode($comune_id))
						->groupBy('tipo','comune')
						->orderBy('tipo')->get();

		$rs['map'] = Impianto::select('impianto','tipo','longitude','latitude')
						->where('comune','=',urldecode($comune_id))
						->get();
						
		return response()->json($rs);
    }
	public function ImpiantixTipoxComune($comune_id,$tipo_id) {
		$rs = [];
		$rs['title'] = urldecode($comune_id).' &gt; '.urldecode($tipo_id);
        $rs['impianti'] = Impianto::select()
						->where('comune','=',urldecode($comune_id))
						->where('tipo','=',urldecode($tipo_id))
						->orderBy('tipo')->get();
		return response()->json($rs);
    }
	public function InterventixImpianto($impianto_id) {
		$rs = [];
		$rs['impianti'] = Impianto::select()->where('id','=',urldecode($impianto_id))->get();
        $rs['interventi'] = Intervento::select('interventi.*','users.name','users.foto','users.tel')
						->join('users', 'users.id', '=', 'user_id')
						->where('impianto_id','=',urldecode($impianto_id))
						->orderBy('in_at','desc')->get();
		return response()->json($rs);
    }
	
	
}
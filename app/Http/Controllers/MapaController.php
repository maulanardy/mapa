<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\MapaBukti;

class MapaController extends Controller
{
    public function get(Request $request){

        $skema  = MapaBukti::where('jenis_bukti', 'T')->whereNull('metode_pa')->distinct()->get(['kode_skema']);

        foreach($skema->toArray() as $sk){
            $unit = DB::select("select m.id, mlmb.*, m.no_elemen, m.no_kuk from (select kode_skema, no_urut_unit_komp  from ms_lsp_mapa_bukti  where jenis_bukti = 'T' and metode_pa is null group by kode_skema, no_urut_unit_komp ) mlmb
            left join (select id, kode_skema, no_urut_unit_komp, no_elemen, no_kuk from ms_lsp_mapa_bukti where jenis_bukti = 'T' and metode_pa is null) m on m.kode_skema = mlmb.kode_skema and m.no_urut_unit_komp = mlmb.no_urut_unit_komp 
            where mlmb.kode_skema = '".$sk['kode_skema']."' order by kode_skema, no_kuk, no_elemen, no_urut_unit_komp limit 30");

            $ids = [];
            foreach($unit as $u){
                $ids[] = $u->id;
            }

            $items = MapaBukti::whereIn('id', $ids)->update(array('metode_pa' => "DPT"));
        }

            return response()->json([
                'code' => 'OK',
                'message' => 'OK',
                'data' => 'Update success'
            ], 200);
    }
}

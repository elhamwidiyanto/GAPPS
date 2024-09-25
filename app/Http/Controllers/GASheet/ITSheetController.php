<?php

namespace App\Http\Controllers\GASheet;

use App\Http\Controllers\Controller;

use App\Models\GASheet\IT;
use App\Models\GASheet\IT_sheet;
use App\Models\GASheet\IT_tx;
use App\Models\GASheet\Simbol_kondisi;
use App\Models\GASheet\Alat;
use App\Models\GASheet\Lokasi;
use App\Models\GASheet\Gedung;
use App\Models\GASheet\IT_history;
use App\Models\GASheet\Ruangan;
use App\Models\GASheet\Pic;
use App\Models\GASheet\Komponen;


use Illuminate\Support\Facades\DB;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Carbon\Carbon;

class ITSheetController extends Controller{
    public $timestamps = false;

    public function __construct()
    {
        $this->middleware('can:it list', ['only' => ['index', 'show']]);
        $this->middleware('can:it create', ['only' => ['create', 'store']]);
        $this->middleware('can:it edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:it delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        //
        $user_id = auth()->user()->id;

        // $master_status = [
        //     [
        //         'id' => 10,
        //         'nama' => 'All'
        //     ],
        //     [
        //         'id' => 1,
        //         'nama' => 'Active'
        //     ],
        //     [
        //         'id' => 4,
        //         'nama' => 'Inactive'
        //     ],
        // ];

        return Inertia::render('GASheet/ITSheet/Index', [
            'app_url' => env('APP_URL'),
            'user_id' => $user_id,
        ]);    
    }

    public function json_index()
    {
        //
        $nip = auth()->user()->nip;
        $user_id = auth()->user()->id;
        $name = auth()->user()->name;
        
        $company_id = auth()->user()->company_id;
        $hasRoles = auth()->user()->getRoleNames();// Returns a collection

        $myRoles = [];
        foreach($hasRoles as $key => $value){
            $myRoles[] = $value;
        }

        $query = IT_sheet::selectRaw("*");

        if (request()->has('search')) {
            $query->where(function($subquery){
                $subquery->where('user_name', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('department_name', 'LIKE', '%'.request()->input('search').'%');
            });
        }

        $data = $query->paginate(5)->onEachSide(2);
    
       
        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }
    public function scan(){
        return Inertia::render('GASheet/ITSheet/Scan', [
            'app_url' => env('APP_URL'),
        ]);
    }

    public function get_komponen_simbol_kondisi(Request $request)
    {
        $location = $request->value_location;
        $gedung = $request->value_gedung;
        $ruangan = $request->value_ruangan;
        $alat = $request->value_alat;
        // dd($alat);

        $query_get_komponen = Komponen::whereRaw("alat_id = ".$alat['id'] ." AND type = " .'3')->get();

        $query_get_simbol_kondisi = Simbol_kondisi::whereRaw("location_id = ".$location['id'] ." AND gedung_id = ".$gedung['id']." AND ruangan_id = ".$ruangan['id']. " AND type  =" .'3')->get();
        
        foreach ($query_get_komponen as $key => $komponen) {
            $simbol_kondisi_ids[$key] = $komponen->default_simbol_id;
        }
        $query_get_default_simbol = collect();

        foreach ($simbol_kondisi_ids as $id) {
            $simbol = Simbol_kondisi::where('id', $id)
                ->where('type', 3)
                ->where('is_active', 1)
                ->first();
            if ($simbol) {
                $query_get_default_simbol->push($simbol);
            }
        }
        // dd($simbol_kondisi_ids);
        return response()->json([
            'success' => true,
            'data_komponen'    => $query_get_komponen,
            'data_simbol_kondisi'    => $query_get_simbol_kondisi,
            'default_simbol_kondisi' => $query_get_default_simbol,
        ]);
    }
    public function history($id){
        $it_tx_history = IT_history::where("it_header_id", $id)
            ->selectRaw("it_tx_history.*, lara_users.name AS user_update_name")
            ->join("lara_users", "lara_users.id", "=", "it_tx_history.user_update_id")
            ->get();        
        // dd($it_tx_history);
        $user_id = auth()->user()->id;
        // dd($it_tx_history);
        return Inertia::render('GASheet/ITHeadSheet/History', [
            'it_histories' => $it_tx_history,
            'user_login' => $user_id,
        ]);
    }

    public function get_komponen_simbol_kondisi_qr(Request $request)
    {
        $alat = $request->value_alat;
        $alat_id = $request->value_alat_id;

        $query_get_komponen = Komponen::whereRaw("alat_id = ".$alat['id'] ." AND type = " .'3')->get();

        $query_get_simbol_kondisi = Simbol_kondisi::whereRaw("is_active = 1 AND type  = 3")->get();
        // dd($query_get_simbol_kondisi);
        
        return response()->json([
            'success' => true,
            'data_komponen'    => $query_get_komponen,
            'data_simbol_kondisi'    => $query_get_simbol_kondisi,
            
        ]);
    }
 
    public function create()
    {
        $master_simbol_kondisis = Simbol_kondisi::whereRaw("is_active = 1 && type = 3")->get();
        $master_alats = Alat::whereRaw("is_active = 1 && type = 3")->get();
        $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
        $master_gedungs = Gedung::whereRaw("is_active = 1")->get();
        $master_ruangans = Ruangan::whereRaw("is_active = 1")->get();
        $master_komponen = Komponen::whereRaw("is_active = 1 AND type = 3")->get();
        $query_get_pic = Pic::whereRaw("is_active = 1 AND type = 3")->get();
        return Inertia::render('GASheet/ITSheet/Create', [
            'app_url' => env('APP_URL'),
            'master_simbol_kondisis' => $master_simbol_kondisis,
            'master_alats' => $master_alats,
            'master_lokasis' => $master_lokasis,
            'master_gedungs' => $master_gedungs,
            'master_ruangans' => $master_ruangans,
            'master_pics'  => $query_get_pic,
            'master_komponens' => $master_komponen,
        ]);
    }

    public function create_qr(Request $request){
        $id_alat = $request->id_alat;
        $alatChoosed = Alat::whereRaw("is_active = 1 && id = $id_alat")->first();
        $master_simbol_kondisis = Simbol_kondisi::whereRaw("is_active = 1 && type = 3")->get();
        $master_pics = Pic::whereRaw("is_active = 1 AND type = 2")->get();
        // $master_alats = Alat::whereRaw("is_active = 1 && type = 2")->get();
        $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
        $master_gedungs = Gedung::whereRaw("is_active = 1")->get();
        $master_ruangans = Ruangan::whereRaw("is_active = 1")->get();
        return Inertia::render('GASheet/ITSheet/Create', [
            'app_url' => env('APP_URL'),
            'master_simbol_kondisis' => $master_simbol_kondisis,
            'master_pics' => $master_pics, 
            // 'master_alats' => $master_alats,
            'master_lokasis' => $master_lokasis,
            'master_gedungs' => $master_gedungs,
            'master_ruangans' => $master_ruangans,
            'alat_choosed' => $alatChoosed,
        ]);
    }
    public function store(Request $request)
    {
        $user_id = auth()->user()->id;

        $today = Carbon::today();

        $itChecks = IT_sheet::where('user_id', $user_id)
            ->where('status', 1)
            ->whereDate('created_at', $today)
            ->first();
        if($itChecks != null){
            return redirect('/ga_sheet/it')->with('Error', 'Anda Sudah Mengajukan!');
        }

        $it_header = new IT_sheet();
        $it_header->date = date("Y-m-d H:i:s");
        $it_header->status = 1;
        $it_header->user_update_id = $user_id;
        $it_header->user_id = auth()->user()->id;
        $it_header->user_nip = auth()->user()->nip;
        $it_header->user_phone = auth()->user()->phone;
        $it_header->user_name = auth()->user()->name;
        $it_header->company_id = auth()->user()->company_id;
        $it_header->company_name = auth()->user()->company_name;
        $it_header->department_name = auth()->user()->department_name;
        $it_header->department_id = auth()->user()->department_id;
        $it_header->pic_id = $request->pic['id'];
        $it_header->pic_name = $request->pic['pic_name'];
        $it_header->pic_nip = $request->pic['pic_nip'];
        $it_header->pic_email = $request->pic['pic_email'];
        $it_header->pic_phone = $request->pic['pic_phone'];
        $it_header->lokasi_id = $request->location['id'];
        $it_header->lokasi_name = $request->location['name'];
        $it_header->alat_id = $request->master_alats['id'];
        $it_header->alat_name = $request->master_alats['name'];
        
        $it_header->gedung_id = $request->gedung['id'];
        $it_header->gedung_name = $request->gedung['name'];

        $it_header->ruangan_id = $request->ruangan['id'];
        $it_header->ruangan_name = $request->ruangan['name'];
        $it_header->save();


        foreach ($request->master_komponens as $key => $value) {
            $it_tx = new IT();
            $it_tx->it_header_id = $it_header->id;
            $it_tx->user_update_id = $user_id;
            $it_tx->date = $it_header->date;
            $it_tx->is_checked = (!empty($request->is_checked[$key])) ? 1 : 0;
            $it_tx->description = (!empty($request->description[$key])) ? $request->description[$key] : null;
            $it_tx->alat_id = $request->master_alats['id'];
            $it_tx->alat_name = $request->master_alats['name'];
            $it_tx->komponen_id = $value['id'];
            $it_tx->komponen_name = $value['name'];
            $it_tx->simbol_kondisi_id = (!empty($request->simbol_name[$key]['id'])) ? $request->simbol_name[$key]['id'] : null;
            $it_tx->simbol_kondisi_name = (!empty($request->simbol_name[$key]['name'])) ? $request->simbol_name[$key]['name'] : null;
            $it_tx->status = $it_header->status;
            $it_tx->user_id = auth()->user()->id;
            $it_tx->user_nip = auth()->user()->nip;
            $it_tx->user_phone = auth()->user()->phone;
            $it_tx->user_name = auth()->user()->name;
            $it_tx->company_id = auth()->user()->company_id;
            $it_tx->company_name = auth()->user()->company_name;
            $it_tx->department_name = auth()->user()->department_name;
            $it_tx->department_id = auth()->user()->department_id;
            $it_tx->pic_id = $request->pic['id'];
            $it_tx->pic_name = $request->pic['pic_name'];
            $it_tx->pic_nip = $request->pic['pic_nip'];
            $it_tx->pic_email = $request->pic['pic_email'];
            $it_tx->pic_phone = $request->pic['pic_phone'];
            $it_tx->lokasi_id = $request->location['id'];
            $it_tx->lokasi_name = $request->location['name'];
            $it_tx->gedung_id = $request->gedung['id'];
            $it_tx->gedung_name = $request->gedung['name'];
            $it_tx->ruangan_id = $request->ruangan['id'];
            $it_tx->ruangan_name = $request->ruangan['name'];
            $it_tx->save();

            $it_history = new IT_history();
            $it_history->history_status = 0;
            $it_history->it_header_id = $it_header->id;
            $it_history->it_tx_id = $it_tx->id;
            $it_history->user_update_id = $user_id;
            $it_history->date = $it_header->date;
            $it_history->is_checked = (!empty($request->is_checked[$key])) ? 1 : 0;
            $it_history->description = (!empty($request->description[$key])) ? $request->description[$key] : null;
            $it_history->alat_id = $request->master_alats['id'];
            $it_history->alat_name = $request->master_alats['name'];
            $it_history->komponen_id = $value['id'];
            $it_history->komponen_name = $value['name'];
            $it_history->simbol_kondisi_id = (!empty($request->simbol_name[$key]['id'])) ? $request->simbol_name[$key]['id'] : null;
            $it_history->simbol_kondisi_name = (!empty($request->simbol_name[$key]['name'])) ? $request->simbol_name[$key]['name'] : null;
            $it_history->status = $it_header->status;
            $it_history->user_id = auth()->user()->id;
            $it_history->user_nip = auth()->user()->nip;
            $it_history->user_phone = auth()->user()->phone;
            $it_history->user_name = auth()->user()->name;
            $it_history->company_id = auth()->user()->company_id;
            $it_history->company_name = auth()->user()->company_name;
            $it_history->department_name = auth()->user()->department_name;
            $it_history->department_id = auth()->user()->department_id;
            $it_history->pic_id = $request->pic['id'];
            $it_history->pic_name = $request->pic['pic_name'];
            $it_history->pic_nip = $request->pic['pic_nip'];
            $it_history->pic_email = $request->pic['pic_email'];
            $it_history->pic_phone = $request->pic['pic_phone'];
            $it_history->lokasi_id = $request->location['id'];
            $it_history->lokasi_name = $request->location['name'];
            $it_history->gedung_id = $request->gedung['id'];
            $it_history->gedung_name = $request->gedung['name'];
            $it_history->ruangan_id = $request->ruangan['id'];
            $it_history->ruangan_name = $request->ruangan['name'];
            $it_history->save();
        }
        
        return redirect('/ga_sheet/it');

    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $it_header = it_sheet::find($id);

        $it_tx = it::whereRaw("it_header_id = $it_header->id")->get();

        $simbol_kondisi_id_arr = [];

        foreach($it_tx as $key => $value){
            $simbol_kondisi_id_arr[] = [
                'id' => $value->simbol_kondisi_id,
                'name' => $value->simbol_kondisi_name,
            ];
        }

        $master_simbol_kondisis = Simbol_kondisi::whereRaw("is_active = 1 && type = 2")->get();

        $query_get_pic = Pic::whereRaw("is_active = 1")->get();
        $query_get_pic_choosed = Pic::whereRaw("id = $it_header->pic_id")->first();

        $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
        $master_lokasi_choosed = Lokasi::whereRaw("id = $it_header->lokasi_id")->first();

        $master_gedungs = Gedung::whereRaw("is_active = 1")->get();
        $master_gedung_choosed = Gedung::whereRaw("id = $it_header->gedung_id")->first();

        $master_ruangans = Ruangan::whereRaw("is_active = 1")->get();
        $master_ruangan_choosed = Ruangan::whereRaw("id = $it_header->ruangan_id")->first();

        $master_alats = Alat::whereRaw("is_active = 1")->get();
        $master_alat_choosed = Alat::whereRaw("id = $it_header->alat_id")->first();

        return Inertia::render('GASheet/ITSheet/Edit', [
            'app_url' => env('APP_URL'),
            'it_header' => $it_header,
            'master_simbol_kondisis' => $master_simbol_kondisis,
            'master_pics'  => $query_get_pic,
            'simbol_kondisi_id_arr' => $simbol_kondisi_id_arr,
            'query_get_pic_choosed' => $query_get_pic_choosed,
            'master_lokasis' => $master_lokasis,
            'master_lokasi_choosed' => $master_lokasi_choosed,
            'master_gedungs' => $master_gedungs,
            'master_gedung_choosed' => $master_gedung_choosed,
            'master_ruangans' => $master_ruangans,
            'master_ruangan_choosed' => $master_ruangan_choosed,
            'it_tx' => $it_tx,
            'master_alats' => $master_alats,
            'master_alat_choosed' => $master_alat_choosed,
            'status' =>$it_header->status,
            // dd($it_header->status)
        ]);
    }
    public function getConditions()
    {
        $conditions = Simbol_kondisi::select('id', 'name')->get();
        return response()->json($conditions);
    }


    /**
     * Update the specified resource in storage.
     */

  
     public function update(Request $request, string $id)    //BELOM DIBUAT
     {
         //
         $it_header = IT_sheet::find($id);
         $user_id = auth()->user()->id;
         // $it_header->date = date("Y-m-d H:i:s");
         
         $it_header->status = $request->status;
 
         // $it_header->user_id = auth()->user()->id;
         // $it_header->user_nip = auth()->user()->nip;
         // $it_header->user_phone = auth()->user()->phone;
         // $it_header->user_name = auth()->user()->name;
         // $it_header->company_id = auth()->user()->company_id;
         // $it_header->company_name = auth()->user()->company_name;
         // $it_header->department_name = auth()->user()->department_name;
         // $it_header->department_id = auth()->user()->department_id;
 
         $it_header->pic_id = $request->pic['id'];
         $it_header->pic_name = $request->pic['pic_name'];
         $it_header->pic_nip = $request->pic['pic_nip'];
         $it_header->pic_email = $request->pic['pic_email'];
         $it_header->pic_phone = $request->pic['pic_phone'];
         $it_header->user_update_id = $user_id;
 
         $it_header->save();
 
         it::whereRaw("it_header_id = $it_header->id")->delete();
 
         foreach ($request->it_tx as $key => $value) {
 
             $it_tx = new IT();
             $it_tx->id = $value['id'];
             $it_tx->it_header_id = $it_header->id;
             $it_tx->date = $it_header->date;
             $it_tx->user_update_id = $user_id;
             $it_tx->is_checked = ($request->is_checked[$key]) ? 1 : 0;
             $it_tx->description = (!empty($request->description[$key])) ? $request->description[$key] : null;
             $it_tx->alat_id = $value['alat_id'];
             $it_tx->alat_name = $value['alat_name'];
             $it_tx->simbol_kondisi_id = (!empty($request->simbol_name[$key]['id'])) ? $request->simbol_name[$key]['id'] : null;
             $it_tx->simbol_kondisi_name = (!empty($request->simbol_name[$key]['name'])) ? $request->simbol_name[$key]['name'] : null;
             $it_tx->status = $it_header->status;
             $it_tx->user_id = auth()->user()->id;
             $it_tx->user_nip = auth()->user()->nip;
             $it_tx->user_phone = auth()->user()->phone;
             $it_tx->user_name = auth()->user()->name;
             $it_tx->company_id = auth()->user()->company_id;
             $it_tx->company_name = auth()->user()->company_name;
             $it_tx->department_name = auth()->user()->department_name;
             $it_tx->department_id = auth()->user()->department_id;
             $it_tx->pic_id = $request->pic['id'];
             $it_tx->pic_name = $request->pic['pic_name'];
             $it_tx->pic_nip = $request->pic['pic_nip'];
             $it_tx->pic_email = $request->pic['pic_email'];
             $it_tx->pic_phone = $request->pic['pic_phone'];
             $it_tx->lokasi_id = $request->location['id'];
             $it_tx->lokasi_name = $request->location['name'];
             $it_tx->gedung_id = $request->gedung['id'];
             $it_tx->gedung_name = $request->gedung['name'];
             $it_tx->ruangan_id = $request->ruangan['id'];
             $it_tx->ruangan_name = $request->ruangan['name'];
             $it_tx->komponen_id = $value['komponen_id'];
             $it_tx->komponen_name = $value['komponen_name'];
             $it_tx->save();

             $it_history = new IT_history();
             $it_history->history_status = $request->status;
             $it_history->it_tx_id = $it_tx->id;
             $it_history->it_header_id = $it_header->id;
             $it_history->date = $it_tx->date;
             $it_history->user_update_id = $user_id;
             $it_history->is_checked = ($request->is_checked[$key]) ? 1 : 0;
             $it_history->description = (!empty($request->description[$key])) ? $request->description[$key] : null;
             $it_history->alat_id = $value['alat_id'];
             $it_history->alat_name = $value['alat_name'];
             $it_history->simbol_kondisi_id = (!empty($request->simbol_name[$key]['id'])) ? $request->simbol_name[$key]['id'] : null;
             $it_history->simbol_kondisi_name = (!empty($request->simbol_name[$key]['name'])) ? $request->simbol_name[$key]['name'] : null;
             $it_history->status = $it_header->status;
             $it_history->user_id = auth()->user()->id;
             $it_history->user_nip = auth()->user()->nip;
             $it_history->user_phone = auth()->user()->phone;
             $it_history->user_name = auth()->user()->name;
             $it_history->company_id = auth()->user()->company_id;
             $it_history->company_name = auth()->user()->company_name;
             $it_history->department_name = auth()->user()->department_name;
             $it_history->department_id = auth()->user()->department_id;
             $it_history->pic_id = $request->pic['id'];
             $it_history->pic_name = $request->pic['pic_name'];
             $it_history->pic_nip = $request->pic['pic_nip'];
             $it_history->pic_email = $request->pic['pic_email'];
             $it_history->pic_phone = $request->pic['pic_phone'];
             $it_history->lokasi_id = $request->location['id'];
             $it_history->lokasi_name = $request->location['name'];
             $it_history->gedung_id = $request->gedung['id'];
             $it_history->gedung_name = $request->gedung['name'];
             $it_history->ruangan_id = $request->ruangan['id'];
             $it_history->ruangan_name = $request->ruangan['name'];
             $it_history->komponen_id = $value['komponen_id'];
             $it_history->komponen_name = $value['komponen_name'];
             $it_history->save();
         }
         
         return redirect('/ga_sheet/it');
         
     }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}



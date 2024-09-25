<?php

namespace App\Http\Controllers\GASheet;

use App\Http\Controllers\Controller;

use App\Models\GASheet\Simbol_kondisi;
use App\Models\GASheet\Alat;
use App\Models\GASheet\Cleaning_sheet;
use App\Models\GASheet\Pic;
use App\Models\GASheet\Cleaning_tx;
use App\Models\GASheet\Cleaning_tx_history;
use App\Models\GASheet\Lokasi;
use App\Models\GASheet\Gedung;
use App\Models\GASheet\Ruangan;

use DB;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Carbon\Carbon;

class CleaningSheetController extends Controller{
    public $timestamps = false;

    public function __construct()
    {
        $this->middleware('can:cleaning list', ['only' => ['index', 'show']]);
        $this->middleware('can:cleaning create', ['only' => ['create', 'store']]);
        $this->middleware('can:cleaning edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:cleaning delete', ['only' => ['destroy']]);
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

        return Inertia::render('GASheet/CleaningSheet/Index', [
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

        $query = Cleaning_sheet::selectRaw("*");

        if (request()->has('search')) {
            $query->where(function($subquery){
                $subquery->where('user_name', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('department_name', 'LIKE', '%'.request()->input('search').'%');
            });
        }

        // if(request('search_status') == 4){

        //     $query->whereRaw('lara_sirkuler_master_pihak_lain.is_active = 0');

        // } else {

        //     if(request('search_status')){
        //         if(request('search_status') != 10){
        //             $query->whereRaw('lara_sirkuler_master_pihak_lain.is_active = '.request('search_status'));
        //         }
        //     }
        // }

        // if (request('field')) {
        //     $query->orderBy(request('field'), request('direction'))->orderBy('Pic.created_at', request('direction'));
        // } else {
        //     $query->orderBy("Pic.id", "desc");
        // }

        $data = $query->paginate(5)->onEachSide(2);
    

        return response()->json([ 
            'success' => true,
            'data'    => $data
        ]);
    }

    public function get_alat_simbol_kondisi(Request $request)
    {
        $location = $request->value_location;
        $gedung = $request->value_gedung;
        $ruangan = $request->value_ruangan;

        // dd($ruangan);
        $query_get_alat = Alat::whereRaw("location_id = ".$location['id'] ." AND gedung_id = ".$gedung['id']." AND ruangan_id = ".$ruangan['id']." AND type  =" .'1')
            ->get();

        $query_get_simbol_kondisi = Simbol_kondisi::whereRaw("location_id = ".$location['id'] ." AND gedung_id = ".$gedung['id']." AND ruangan_id = ".$ruangan['id']. " AND type  =" .'1')->get();
        // dd($query_get_alat);
        foreach ($query_get_alat as $key => $alat) {
            $simbol_kondisi_ids[$key] = $alat->default_simbol_id;
            
        }

        $query_get_default_simbol = collect();

        foreach ($simbol_kondisi_ids as $id) {
            $simbol = Simbol_kondisi::where('id', $id)
                ->where('type', 1)
                ->where('is_active', 1)
                ->first();
            if ($simbol) {
                $query_get_default_simbol->push($simbol);
            }
        }
        // dd($query_get_default_simbol);
        
        return response()->json([
            'success' => true,
            'data_alat'    => $query_get_alat,
            'data_simbol_kondisi'    => $query_get_simbol_kondisi,
            'default_simbol_kondisi' => $query_get_default_simbol,
        ]);
    }
    public function history($id){
        $cleaning_tx_history = Cleaning_tx_history::where("cleaning_header_id", $id)
            ->selectRaw("cleaning_tx_history.*, lara_users.name AS user_update_name")
            ->join("lara_users", "lara_users.id", "=", "cleaning_tx_history.user_update_id")
            ->get();        
        // dd($cleaning_tx_history);
        $user_id = auth()->user()->id;
        // dd($cleaning_tx_history);
        return Inertia::render('GASheet/CleaningHeadSheet/History', [
            'cleaning_histories' => $cleaning_tx_history,
            'user_login' => $user_id,
        ]);
    }

    public function create()
    {
        $master_simbol_kondisis = Simbol_kondisi::whereRaw("is_active = 1 && type = 1")->get();
        $master_pics = Pic::whereRaw("type = 1")->get();
        // $master_alats = Alat::whereRaw("is_active = 1 && type = 2")->get();
        $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
        $master_gedungs = Gedung::whereRaw("is_active = 1")->get();
        $master_ruangans = Ruangan::whereRaw("is_active = 1")->get();
        return Inertia::render('GASheet/CleaningSheet/Create', [
            'app_url' => env('APP_URL'),
            'master_simbol_kondisis' => $master_simbol_kondisis,
            'master_pics' => $master_pics, 
            // 'master_alats' => $master_alats,
            'master_lokasis' => $master_lokasis,
            'master_gedungs' => $master_gedungs,
            'master_ruangans' => $master_ruangans,
        ]);
    }

    public function create_qr(Request $request){
        $id_ruangan = $request->id_ruangan;
        // dd($id_ruangan);
        // $id_lokasi = $request->id_lokasi;
        // $id_gedung = $request->id_gedung;
        $master_simbol_kondisis = Simbol_kondisi::whereRaw("is_active = 1 && type = 1")->get();
        $master_pics = Pic::whereRaw("is_active = 1 AND type = 1")->get();
        // $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
        // $lokasi_choosed = Lokasi::whereRaw("id = $id_lokasi")->first();
        // $gedung_choosed = Gedung::whereRaw("id = $id_gedung")->first();
        $ruangan_choosed = Ruangan::whereRaw("id = $id_ruangan")->first();
        // dd($ruangan_choosed);
        return Inertia::render('GASheet/CleaningSheet/Create', [
            'app_url' => env('APP_URL'),
            'master_simbol_kondisis' => $master_simbol_kondisis,
            'master_pics' => $master_pics, 
            // 'master_lokasis' => $master_lokasis,
            // 'lokasi_choosed' => $lokasi_choosed,
            // 'gedung_choosed' => $gedung_choosed,
            'ruangan_choosed' => $ruangan_choosed,
        ]);
    }

    public function get_lokasi_gedung(Request $request){
        $ruangan = $request->ruangan;
        // dd($ruangan);
        $id_gedung = $ruangan['id_gedung'];

        // Find the corresponding gedung and lokasi
        $gedung = Gedung::find($id_gedung);
        $lokasi = Lokasi::find($gedung->id_lokasi);
    
        return response()->json([
            'success' => true,
            'lokasi_choosed' => $lokasi,
            'gedung_choosed' => $gedung,
        ]);
    }


    public function store(Request $request)
    {
        $user_id = auth()->user()->id;

        $cleaning_header = new Cleaning_sheet();
        $cleaning_header->date = date("Y-m-d H:i:s");
        $cleaning_header->status = 1;
        $cleaning_header->user_id = auth()->user()->id;
        $cleaning_header->user_update_id = $user_id;
        $cleaning_header->user_nip = auth()->user()->nip;
        $cleaning_header->user_phone = auth()->user()->phone;
        $cleaning_header->user_name = auth()->user()->name;
        $cleaning_header->company_id = auth()->user()->company_id;
        $cleaning_header->company_name = auth()->user()->company_name;
        $cleaning_header->department_name = auth()->user()->department_name;
        $cleaning_header->department_id = auth()->user()->department_id;
        $cleaning_header->pic_id = $request->pic['id'];
        $cleaning_header->pic_name = $request->pic['pic_name'];
        $cleaning_header->pic_nip = $request->pic['pic_nip'];
        $cleaning_header->pic_email = $request->pic['pic_email'];
        $cleaning_header->pic_phone = $request->pic['pic_phone'];
        $cleaning_header->lokasi_id = $request->location['id'];
        $cleaning_header->lokasi_name = $request->location['name'];
        
        $cleaning_header->gedung_id = $request->gedung['id'];
        $cleaning_header->gedung_name = $request->gedung['name'];

        $cleaning_header->ruangan_id = $request->ruangan['id'];
        $cleaning_header->ruangan_name = $request->ruangan['name'];
        $cleaning_header->save();


        foreach ($request->master_alats as $key => $value) {
            $cleaning_tx = new Cleaning_tx();
            $cleaning_tx->cleaning_header_id = $cleaning_header->id;
            $cleaning_tx->date = $cleaning_header->date;
            $cleaning_tx->user_update_id = $user_id;
            $cleaning_tx->is_checked = (!empty($request->is_checked[$key])) ? 1 : 0;
            $cleaning_tx->description = (!empty($request->description[$key])) ? $request->description[$key] : null;
            $cleaning_tx->alat_id = $value['id'];
            $cleaning_tx->alat_name = $value['name'];
            $cleaning_tx->simbol_kondisi_id = (!empty($request->simbol_name[$key]['id'])) ? $request->simbol_name[$key]['id'] : null;
            $cleaning_tx->simbol_kondisi_name = (!empty($request->simbol_name[$key]['name'])) ? $request->simbol_name[$key]['name'] : null;
            $cleaning_tx->status = $cleaning_header->status;
            $cleaning_tx->user_id = auth()->user()->id;
            $cleaning_tx->user_nip = auth()->user()->nip;
            $cleaning_tx->user_phone = auth()->user()->phone;
            $cleaning_tx->user_name = auth()->user()->name;
            $cleaning_tx->company_id = auth()->user()->company_id;
            $cleaning_tx->company_name = auth()->user()->company_name;
            $cleaning_tx->department_name = auth()->user()->department_name;
            $cleaning_tx->department_id = auth()->user()->department_id;
            $cleaning_tx->pic_id = $request->pic['id'];
            $cleaning_tx->pic_name = $request->pic['pic_name'];
            $cleaning_tx->pic_nip = $request->pic['pic_nip'];
            $cleaning_tx->pic_email = $request->pic['pic_email'];
            $cleaning_tx->pic_phone = $request->pic['pic_phone'];
            $cleaning_tx->lokasi_id = $request->location['id'];
            $cleaning_tx->lokasi_name = $request->location['name'];
            $cleaning_tx->gedung_id = $request->gedung['id'];
            $cleaning_tx->gedung_name = $request->gedung['name'];
            $cleaning_tx->ruangan_id = $request->ruangan['id'];
            $cleaning_tx->ruangan_name = $request->ruangan['name'];
            $cleaning_tx->save();

            $cleaning_history = new Cleaning_tx_history();
            $cleaning_history->cleaning_header_id = $cleaning_header->id;
            $cleaning_history->date = $cleaning_header->date;
            $cleaning_history->cleaning_tx_id = $cleaning_tx->id;
            $cleaning_history->history_status = 0;
            $cleaning_history->user_update_id = $user_id;
            $cleaning_history->is_checked = (!empty($request->is_checked[$key])) ? 1 : 0;
            $cleaning_history->description = (!empty($request->description[$key])) ? $request->description[$key] : null;
            $cleaning_history->alat_id = $value['id'];
            $cleaning_history->alat_name = $value['name'];
            $cleaning_history->simbol_kondisi_id = (!empty($request->simbol_name[$key]['id'])) ? $request->simbol_name[$key]['id'] : null;
            $cleaning_history->simbol_kondisi_name = (!empty($request->simbol_name[$key]['name'])) ? $request->simbol_name[$key]['name'] : null;
            $cleaning_history->status = $cleaning_header->status;
            $cleaning_history->user_id = auth()->user()->id;
            $cleaning_history->user_nip = auth()->user()->nip;
            $cleaning_history->user_phone = auth()->user()->phone;
            $cleaning_history->user_name = auth()->user()->name;
            $cleaning_history->company_id = auth()->user()->company_id;
            $cleaning_history->company_name = auth()->user()->company_name;
            $cleaning_history->department_name = auth()->user()->department_name;
            $cleaning_history->department_id = auth()->user()->department_id;
            $cleaning_history->pic_id = $request->pic['id'];
            $cleaning_history->pic_name = $request->pic['pic_name'];
            $cleaning_history->pic_nip = $request->pic['pic_nip'];
            $cleaning_history->pic_email = $request->pic['pic_email'];
            $cleaning_history->pic_phone = $request->pic['pic_phone'];
            $cleaning_history->lokasi_id = $request->location['id'];
            $cleaning_history->lokasi_name = $request->location['name'];
            $cleaning_history->gedung_id = $request->gedung['id'];
            $cleaning_history->gedung_name = $request->gedung['name'];
            $cleaning_history->ruangan_id = $request->ruangan['id'];
            $cleaning_history->ruangan_name = $request->ruangan['name'];
            $cleaning_history->save();
        }
        
        return redirect('/ga_sheet/cleaning');

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
        $cleaning_header = Cleaning_sheet::find($id);

        $cleaning_tx = Cleaning_tx::whereRaw("cleaning_header_id = $cleaning_header->id")->get();

        $simbol_kondisi_id_arr = [];

        foreach($cleaning_tx as $key => $value){
            $simbol_kondisi_id_arr[] = [
                'id' => $value->simbol_kondisi_id,
                'name' => $value->simbol_kondisi_name,
            ];
        }

        $master_simbol_kondisis = Simbol_kondisi::whereRaw("is_active = 1 && type = 1")->get();

        $query_get_pic = Pic::whereRaw("is_active = 1")->get();
        $query_get_pic_choosed = Pic::whereRaw("id = $cleaning_header->pic_id")->first();

        $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
        $master_lokasi_choosed = Lokasi::whereRaw("id = $cleaning_header->lokasi_id")->first();

        $master_gedungs = Gedung::whereRaw("is_active = 1")->get();
        $master_gedung_choosed = Gedung::whereRaw("id = $cleaning_header->gedung_id")->first();

        $master_ruangans = Ruangan::whereRaw("is_active = 1")->get();
        $master_ruangan_choosed = Ruangan::whereRaw("id = $cleaning_header->ruangan_id")->first();

        return Inertia::render('GASheet/CleaningSheet/Edit', [
            'app_url' => env('APP_URL'), 
            'cleaning_header' => $cleaning_header,
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
            'cleaning_tx' => $cleaning_tx,
            'status' =>$cleaning_header->status,
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

    public function scan(){
        return Inertia::render('GASheet/CleaningSheet/Scan', [
            'app_url' => env('APP_URL'),
        ]); 
    } 
  
    public function update(Request $request, string $id)    //BELOM DIBUAT
    {
        //
        $cleaning_header = Cleaning_sheet::find($id);
        $user_id = auth()->user()->id;
        // $cleaning_header->date = date("Y-m-d H:i:s");
        
        $cleaning_header->status = 1;

        // $cleaning_header->user_id = auth()->user()->id;
        // $cleaning_header->user_nip = auth()->user()->nip;
        // $cleaning_header->user_phone = auth()->user()->phone;
        // $cleaning_header->user_name = auth()->user()->name;
        // $cleaning_header->company_id = auth()->user()->company_id;
        // $cleaning_header->company_name = auth()->user()->company_name;
        // $cleaning_header->department_name = auth()->user()->department_name;
        // $cleaning_header->department_id = auth()->user()->department_id;

        $cleaning_header->pic_id = $request->pic['id'];
        $cleaning_header->pic_name = $request->pic['pic_name'];
        $cleaning_header->pic_nip = $request->pic['pic_nip'];
        $cleaning_header->pic_email = $request->pic['pic_email'];
        $cleaning_header->pic_phone = $request->pic['pic_phone'];
        $cleaning_header->user_update_id = $user_id;

        // $cleaning_header->lokasi_id = $request->location['id'];
        // $cleaning_header->lokasi_name = $request->location['name'];
        
        // $cleaning_header->gedung_id = $request->gedung['id'];
        // $cleaning_header->gedung_name = $request->gedung['name'];

        // $cleaning_header->ruangan_id = $request->ruangan['id'];
        // $cleaning_header->ruangan_name = $request->ruangan['name'];
        $cleaning_header->save();

        Cleaning_tx::whereRaw("cleaning_header_id = $cleaning_header->id")->delete();

        foreach ($request->cleaning_tx as $key => $value) {

            $cleaning_tx = new Cleaning_tx();
            $cleaning_tx->id = $value['id'];
            $cleaning_tx->cleaning_header_id = $cleaning_header->id;
            $cleaning_tx->date = $cleaning_header->date;
            $cleaning_tx->is_checked = ($request->is_checked[$key]) ? 1 : 0;
            $cleaning_tx->description = (!empty($request->description[$key])) ? $request->description[$key] : null;
            $cleaning_tx->alat_id = $value['alat_id'];
            $cleaning_tx->alat_name = $value['alat_name'];
            $cleaning_tx->simbol_kondisi_id = (!empty($request->simbol_name[$key]['id'])) ? $request->simbol_name[$key]['id'] : null;
            $cleaning_tx->simbol_kondisi_name = (!empty($request->simbol_name[$key]['name'])) ? $request->simbol_name[$key]['name'] : null;
            $cleaning_tx->status = $cleaning_header->status;
            $cleaning_tx->user_id = auth()->user()->id;
            $cleaning_tx->user_nip = auth()->user()->nip;
            $cleaning_tx->user_phone = auth()->user()->phone;
            $cleaning_tx->user_name = auth()->user()->name;
            $cleaning_tx->company_id = auth()->user()->company_id;
            $cleaning_tx->company_name = auth()->user()->company_name;
            $cleaning_tx->department_name = auth()->user()->department_name;
            $cleaning_tx->department_id = auth()->user()->department_id;
            $cleaning_tx->pic_id = $request->pic['id'];
            $cleaning_tx->pic_name = $request->pic['pic_name'];
            $cleaning_tx->pic_nip = $request->pic['pic_nip'];
            $cleaning_tx->pic_email = $request->pic['pic_email'];
            $cleaning_tx->pic_phone = $request->pic['pic_phone'];
            $cleaning_tx->lokasi_id = $request->location['id'];
            $cleaning_tx->lokasi_name = $request->location['name'];
            $cleaning_tx->gedung_id = $request->gedung['id'];
            $cleaning_tx->gedung_name = $request->gedung['name'];
            $cleaning_tx->ruangan_id = $request->ruangan['id'];
            $cleaning_tx->ruangan_name = $request->ruangan['name'];
            $cleaning_tx->save();
            
            $cleaning_history = new Cleaning_tx_history();
            $cleaning_history->history_status = 1;
            $cleaning_history->cleaning_tx_id = $cleaning_tx->id;
            $cleaning_history->cleaning_header_id = $cleaning_header->id;
            $cleaning_history->date = $cleaning_header->date;
            $cleaning_history->user_update_id = $user_id;
            $cleaning_history->is_checked = ($request->is_checked[$key]) ? 1 : 0;
            $cleaning_history->description = (!empty($request->description[$key])) ? $request->description[$key] : null;
            $cleaning_history->alat_id = $value['alat_id'];
            $cleaning_history->alat_name = $value['alat_name'];
            $cleaning_history->simbol_kondisi_id = (!empty($request->simbol_name[$key]['id'])) ? $request->simbol_name[$key]['id'] : null;
            $cleaning_history->simbol_kondisi_name = (!empty($request->simbol_name[$key]['name'])) ? $request->simbol_name[$key]['name'] : null;
            $cleaning_history->status = $cleaning_header->status;
            $cleaning_history->user_id = auth()->user()->id;
            $cleaning_history->user_nip = auth()->user()->nip;
            $cleaning_history->user_phone = auth()->user()->phone;
            $cleaning_history->user_name = auth()->user()->name;
            $cleaning_history->company_id = auth()->user()->company_id;
            $cleaning_history->company_name = auth()->user()->company_name;
            $cleaning_history->department_name = auth()->user()->department_name;
            $cleaning_history->department_id = auth()->user()->department_id;
            $cleaning_history->pic_id = $request->pic['id'];
            $cleaning_history->pic_name = $request->pic['pic_name'];
            $cleaning_history->pic_nip = $request->pic['pic_nip'];
            $cleaning_history->pic_email = $request->pic['pic_email'];
            $cleaning_history->pic_phone = $request->pic['pic_phone'];
            $cleaning_history->lokasi_id = $request->location['id'];
            $cleaning_history->lokasi_name = $request->location['name'];
            $cleaning_history->gedung_id = $request->gedung['id'];
            $cleaning_history->gedung_name = $request->gedung['name'];
            $cleaning_history->ruangan_id = $request->ruangan['id'];
            $cleaning_history->ruangan_name = $request->ruangan['name'];
            $cleaning_history->save();
        }
        
        return redirect('/ga_sheet/cleaning');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}


 
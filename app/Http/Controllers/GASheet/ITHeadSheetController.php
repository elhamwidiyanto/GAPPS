<?php

namespace App\Http\Controllers\GASheet;

use App\Http\Controllers\Controller;

use App\Models\GASheet\Simbol_kondisi;
use App\Models\GASheet\Alat;
use App\Models\GASheet\Pic;
use App\Models\GASheet\IT;
use App\Models\GASheet\IT_sheet;
use App\Models\GASheet\IT_history;
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

class ITHeadSheetController extends Controller{
    public $timestamps = false;

    public function __construct()
    {
        $this->middleware('can:it_head list', ['only' => ['index', 'show']]);
        $this->middleware('can:it_head create', ['only' => ['create', 'store']]);
        $this->middleware('can:it_head edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:it_head delete', ['only' => ['destroy']]);
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
        $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
        $it_header = IT_sheet::get();

        return Inertia::render('GASheet/ITHeadSheet/Index', [
            'app_url' => env('APP_URL'),
            'master_lokasis' => $master_lokasis,
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

        // $query = Cleaning_tx::selectRaw("cleaning_tx.*, lara_users.name AS user_name")->join("lara_users","lara_users.id","=","cleaning_tx.user_id");

        // if (request()->has('search')) {
        //     $query->where(function($subquery){
        //         $subquery->where('lokasi_name', 'LIKE', '%'.request()->input('search').'%')
        //         ->orWhere('user_name', 'LIKE', '%'.request()->input('search').'%');
        //     });
        // }
        $query = IT_sheet::selectRaw("IT_header.*, lara_users.name AS user_name")->join("lara_users","lara_users.id","=","IT_header.user_id");

        if (request()->has('search')) {
            $query->where(function($subquery){
                $subquery->where('IT_header.alat_name', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('lara_users.name', 'LIKE', '%'.request()->input('search').'%');
            });
        }

        if (request()->has('year')) {
            $year = request()->input('year');
            $query->whereYear('IT_tx.date', $year);
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

        $query_get_alat = Alat::whereRaw("location_id = ".$location['id'] ." AND gedung_id = ".$gedung['id']." AND ruangan_id = ".$ruangan['id']." AND type  =" .'1')->get();

        $query_get_simbol_kondisi = Simbol_kondisi::whereRaw("location_id = ".$location['id'] ." AND gedung_id = ".$gedung['id']." AND ruangan_id = ".$ruangan['id']. " AND type  =" .'1')->get();

        return response()->json([
            'success' => true,
            'data_alat'    => $query_get_alat,
            'data_simbol_kondisi'    => $query_get_simbol_kondisi,
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
        return Inertia::render('GASheet/ITSheet/Create', [
            'app_url' => env('APP_URL'),
            'master_simbol_kondisis' => $master_simbol_kondisis,
            'master_pics' => $master_pics, 
            // 'master_alats' => $master_alats,
            'master_lokasis' => $master_lokasis,
            'master_gedungs' => $master_gedungs,
            'master_ruangans' => $master_ruangans,
        ]);
    }


    public function status(Request $request) {
        $it_header = IT_sheet::find($request->id);
        $user_id = auth()->user()->id;
        if (!$it_header) {
            return response()->json([
                'message' => 'it header not found',
            ], 404);
        }
        $it_header->reason_reject = (!empty($request->reason_reject)) ? $request->reason_reject : null;
        $it_header->status = $request->status;
        $it_header->user_update_id = $user_id;
        $it_header->save();


        $it_tx = IT::whereRaw("it_header_id = ".$it_header['id'])->get();
        foreach ($it_tx as $key => $value) {
            $it_tx_update = it::find($it_tx[$key]);
            $it_tx_update->status = $request->status;
            $it_tx_update->user_update_id = $user_id;
            $it_history = new it_history();
            $it_history->history_status = (!empty($request->reason_reject)) ? 3 : 2;
            $it_history->it_header_id = $it_header['id'];
            $it_history->komponen_id = $value['komponen_id'];
            $it_history->komponen_name = $value['komponen_name'];
            $it_history->description = $value['description'];
            $it_history->it_tx_id = $value->id;
            $it_history->reason_reject = (!empty($request->reason_reject)) ? $request->reason_reject : null;
            $it_history->date = $value->date;
             $it_history->is_checked = $value['is_checked'];
             $it_history->user_update_id = $user_id;
             $it_history->alat_id = $value['alat_id'];
             $it_history->alat_name = $value['alat_name'];
             $it_history->simbol_kondisi_id = $value['simbol_kondisi_id'];
             $it_history->simbol_kondisi_name = $value['simbol_kondisi_name'];
             $it_history->status = $request->status;
             $it_history->user_id = auth()->user()->id;
             $it_history->user_nip = auth()->user()->nip;
             $it_history->user_phone = auth()->user()->phone;
             $it_history->user_name = auth()->user()->name;
             $it_history->company_id = auth()->user()->company_id;
             $it_history->company_name = auth()->user()->company_name;
             $it_history->department_name = auth()->user()->department_name;
             $it_history->department_id = auth()->user()->department_id;
             $it_history->pic_id = $it_header->pic_id;
             $it_history->pic_name = $it_header->pic_name;
             $it_history->pic_nip = $it_header->pic_nip;
             $it_history->pic_email = $it_header->pic_email;
             $it_history->pic_phone = $it_header->pic_phone;
             $it_history->lokasi_id = $value->lokasi_id;
             $it_history->lokasi_name = $value->lokasi_name;
             $it_history->gedung_id = $value->gedung_id;
             $it_history->gedung_name = $value->gedung_name;
             $it_history->ruangan_id = $value->ruangan_id;
             $it_history->ruangan_name = $value->ruangan_name;
             $it_history->save();
        }
    
    
        return response()->json([
            'message' => 'Status updated successfully',
            'it_header' => $it_header
        ]);
    }
    

    public function store(Request $request)
    {
        //     // Validasi input
        // $request->validate([
        //     'date' => 'required|date',
        //     'status' => 'required',
        //     'form.value_location' => 'required',
        //     'form.value_gedung' => 'required',
        //     'form.value_ruangan' => 'required',
        //     // Validasi lainnya jika diperlukan
        // ]);

        // // Cek apakah kombinasi data sudah ada
        // $exists = Cleaning_tx::where('date', $request->date)
        //     ->where('status', $request->status)
        //     ->where('form.value_location', $request->lokasi)
        //     ->where('form.value_gedung', $request->gedung)
        //     ->where('form.value_ruangan', $request->ruangan)
        //     ->exists();

        // if ($exists) {
        //     return response()->json([
        //         'error' => 'Data dengan kombinasi tersebut sudah ada.'
        //     ], 422);
        // }
        //Untuk nilai NULL pada array jika tidak dipilih
        $simbol_names = array_map(function($simbol) {
            return $simbol['name'] ?? null;
        }, $request->simbol_name);
    
        $simbol_ids = array_map(function($simbol) {
            return $simbol['id'] ?? null;
        }, $request->simbol_name);
    
    
        $user_id = auth()->user()->id;

        $query_get_user_info = it::join('lara_users', 'lara_users.id', '=', 'cleaning_tx.user_id')
            ->select('cleaning_tx.*', 'lara_users.*')
            ->get();
        
            
        // GET ID AND NAME FROM ARRAY
        $alat_names = array_column($request->master_alats, 'name');
        $alat_ids = array_column($request->master_alats, 'id');
        $simbol_ids = array_column($request->simbol_name, 'id');
        $simbol_names = array_column($request->simbol_name, 'name');
        $is_checked = $request->is_checked;
        $pic = $request->pic;
        
        // dd($simbol_names, $alat_names, $request->is_checked);
    
        foreach ($is_checked as $index => $is_check) {
            if ($is_checked[$index] == 1 && $is_checked[$index] !== null) {
                $it = new it();
                $it->user_update_id = $user_id;
                $it->date = Carbon::now();
                $it->alat_name = $alat_names[$index];
                $it->alat_id = $alat_ids[$index]; 
                $it->simbol_kondisi_name = $simbol_names[$index]; 
                $it->simbol_kondisi_id = $simbol_ids[$index]; 
                $it->description = $request->description[$index]; 
                $it->pic_id = $pic['id'];
                $it->pic_nip = $pic['pic_nip'];
                $it->pic_name = $pic['pic_name'];
                $it->pic_email = $pic['pic_email'];
                $it->pic_phone = $pic['pic_phone'];
                $it->user_id = $user_id;
                $it->status = 1;
                $it->is_checked = 1;
                $it->save();

                $it_tx_history = new it_history();
                $it_tx_history->date = Carbon::now();
                $it_tx_history->user_update_id = $user_id;
                $it_tx_history->alat_name = $alat_names[$index];
                $it_tx_history->alat_id = $alat_ids[$index]; 
                $it_tx_history->simbol_kondisi_name = $simbol_names[$index]; 
                $it_tx_history->simbol_kondisi_id = $simbol_ids[$index]; 
                $it_tx_history->description = $request->description[$index]; 
                $it_tx_history->pic_id = $pic['id'];
                $it_tx_history->pic_nip = $pic['pic_nip'];
                $it_tx_history->pic_name = $pic['pic_name'];
                $it_tx_history->pic_email = $pic['pic_email'];
                $it_tx_history->pic_phone = $pic['pic_phone'];
                $it_tx_history->user_id = $user_id;
                $it_tx_history->status = 1;
                $it_tx_history->is_checked = 1;
                $it_tx_history->save();
            } 

        }
    
        // redirect
        return redirect('/ga_sheet/it_head');
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

        return Inertia::render('GASheet/ITHeadSheet/Edit', [
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
        $user_id = auth()->user()->id;
        // dd($request->company_name);


        $it = it::find($id);
        $it->pic_name = $request->pic_name;
        $it->pic_phone = $request->pic_phone;
        $it->pic_email = $request->pic_email;
        $it->pic_nip = $request->pic_nip;
        $it->user_id = $user_id;
        $it->company_name = $request->company_name;
        $it->departement_name = $request->departement_name;
        // if($request->is_active){

        //     $Pic->is_active = 1;
        // } else {
        //     $Pic->is_active = 0;
        // }

        $it->save();
        

        //redirect
        // return redirect('/ga_sheet/pic');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}


 
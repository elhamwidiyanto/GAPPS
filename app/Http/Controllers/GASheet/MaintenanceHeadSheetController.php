<?php

namespace App\Http\Controllers\GASheet;

use App\Http\Controllers\Controller;

use App\Models\GASheet\Simbol_kondisi;
use App\Models\GASheet\Alat;
use App\Models\GASheet\Pic;
use App\Models\GASheet\Maintenance;
use App\Models\GASheet\Maintenance_sheet;
use App\Models\GASheet\Maintenance_history;
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

class MaintenanceHeadSheetController extends Controller{
    public $timestamps = false;

    public function __construct()
    {
        $this->middleware('can:maintenance_head list', ['only' => ['index', 'show']]);
        $this->middleware('can:maintenance_head create', ['only' => ['create', 'store']]);
        $this->middleware('can:maintenance_head edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:maintenance_head delete', ['only' => ['destroy']]);
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
        $maintenance_header = Maintenance_sheet::get();

        return Inertia::render('GASheet/MaintenanceHeadSheet/Index', [
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
        $query = Maintenance_sheet::selectRaw("*");

        if (request()->has('search')) {
            $query->where(function($subquery){
                $subquery->where('user_name', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('department_name', 'LIKE', '%'.request()->input('search').'%');
            });
        }

        if (request()->has('year')) {
            $year = request()->input('year');
            $query->whereYear('maintenance_sheet.date', $year);
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
        return Inertia::render('GASheet/MaintenanceSheet/Create', [
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
        $maintenance_header = Maintenance_sheet::find($request->id);
        $user_id = auth()->user()->id;
        if (!$maintenance_header) {
            return response()->json([
                'message' => 'maintenance header not found',
            ], 404);
        }
        $maintenance_header->reason_reject = (!empty($request->reason_reject)) ? $request->reason_reject : null;
        $maintenance_header->status = $request->status;
        $maintenance_header->user_update_id = $user_id;
        $maintenance_header->save();


        $maintenance_tx = maintenance::whereRaw("maintenance_header_id = ".$maintenance_header['id'])->get();
        foreach ($maintenance_tx as $key => $value) {
            $maintenance_tx_update = maintenance::find($maintenance_tx[$key]);
            $maintenance_tx_update->status = $request->status;
            $maintenance_tx_update->user_update_id = $user_id;
            $maintenance_history = new maintenance_history();
            $maintenance_history->history_status = (!empty($request->reason_reject)) ? 3 : 2;
            $maintenance_history->maintenance_header_id = $maintenance_header['id'];
            $maintenance_history->komponen_id = $value['komponen_id'];
            $maintenance_history->komponen_name = $value['komponen_name'];
            $maintenance_history->description = $value['description'];
            $maintenance_history->maintenance_tx_id = $value->id;
            $maintenance_history->reason_reject = (!empty($request->reason_reject)) ? $request->reason_reject : null;
            $maintenance_history->date = $value->date;
             $maintenance_history->is_checked = $value['is_checked'];
             $maintenance_history->user_update_id = $user_id;
             $maintenance_history->alat_id = $value['alat_id'];
             $maintenance_history->alat_name = $value['alat_name'];
             $maintenance_history->serial_number = $value['serial_number'];
             $maintenance_history->simbol_kondisi_id = $value['simbol_kondisi_id'];
             $maintenance_history->simbol_kondisi_name = $value['simbol_kondisi_name'];
             $maintenance_history->status = $request->status;
             $maintenance_history->user_id = auth()->user()->id;
             $maintenance_history->user_nip = auth()->user()->nip;
             $maintenance_history->user_phone = auth()->user()->phone;
             $maintenance_history->user_name = auth()->user()->name;
             $maintenance_history->company_id = auth()->user()->company_id;
             $maintenance_history->company_name = auth()->user()->company_name;
             $maintenance_history->department_name = auth()->user()->department_name;
             $maintenance_history->department_id = auth()->user()->department_id;
             $maintenance_history->pic_id = $maintenance_header->pic_id;
             $maintenance_history->pic_name = $maintenance_header->pic_name;
             $maintenance_history->pic_nip = $maintenance_header->pic_nip;
             $maintenance_history->pic_email = $maintenance_header->pic_email;
             $maintenance_history->pic_phone = $maintenance_header->pic_phone;
             $maintenance_history->lokasi_id = $value->lokasi_id;
             $maintenance_history->lokasi_name = $value->lokasi_name;
             $maintenance_history->gedung_id = $value->gedung_id;
             $maintenance_history->gedung_name = $value->gedung_name;
             $maintenance_history->ruangan_id = $value->ruangan_id;
             $maintenance_history->ruangan_name = $value->ruangan_name;
             $maintenance_history->save();
        }
    
    
        return response()->json([
            'message' => 'Status updated successfully',
            'maintenance_header' => $maintenance_header
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

        $query_get_user_info = Maintenance::join('lara_users', 'lara_users.id', '=', 'cleaning_tx.user_id')
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
                $maintenance = new Maintenance();
                $maintenance->user_update_id = $user_id;
                $maintenance->date = Carbon::now();
                $maintenance->alat_name = $alat_names[$index];
                $maintenance->alat_id = $alat_ids[$index]; 
                $maintenance->simbol_kondisi_name = $simbol_names[$index]; 
                $maintenance->simbol_kondisi_id = $simbol_ids[$index]; 
                $maintenance->description = $request->description[$index]; 
                $maintenance->pic_id = $pic['id'];
                $maintenance->pic_nip = $pic['pic_nip'];
                $maintenance->pic_name = $pic['pic_name'];
                $maintenance->pic_email = $pic['pic_email'];
                $maintenance->pic_phone = $pic['pic_phone'];
                $maintenance->user_id = $user_id;
                $maintenance->status = 1;
                $maintenance->is_checked = 1;
                $maintenance->save();

                $maintenance_tx_history = new Maintenance_history();
                $maintenance_tx_history->date = Carbon::now();
                $maintenance_tx_history->user_update_id = $user_id;
                $maintenance_tx_history->alat_name = $alat_names[$index];
                $maintenance_tx_history->alat_id = $alat_ids[$index]; 
                $maintenance_tx_history->simbol_kondisi_name = $simbol_names[$index]; 
                $maintenance_tx_history->simbol_kondisi_id = $simbol_ids[$index]; 
                $maintenance_tx_history->description = $request->description[$index]; 
                $maintenance_tx_history->pic_id = $pic['id'];
                $maintenance_tx_history->pic_nip = $pic['pic_nip'];
                $maintenance_tx_history->pic_name = $pic['pic_name'];
                $maintenance_tx_history->pic_email = $pic['pic_email'];
                $maintenance_tx_history->pic_phone = $pic['pic_phone'];
                $maintenance_tx_history->user_id = $user_id;
                $maintenance_tx_history->status = 1;
                $maintenance_tx_history->is_checked = 1;
                $maintenance_tx_history->save();
            } 

        }
    
        // redirect
        return redirect('/ga_sheet/maintenance_head');
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
        $maintenance_header = maintenance_sheet::find($id);

        $maintenance_tx = maintenance::whereRaw("maintenance_header_id = $maintenance_header->id")->get();

        $simbol_kondisi_id_arr = [];

        foreach($maintenance_tx as $key => $value){
            $simbol_kondisi_id_arr[] = [
                'id' => $value->simbol_kondisi_id,
                'name' => $value->simbol_kondisi_name,
            ];
        }

        $master_simbol_kondisis = Simbol_kondisi::whereRaw("is_active = 1 && type = 2")->get();

        $query_get_pic = Pic::whereRaw("is_active = 1")->get();
        $query_get_pic_choosed = Pic::whereRaw("id = $maintenance_header->pic_id")->first();

        $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
        $master_lokasi_choosed = Lokasi::whereRaw("id = $maintenance_header->lokasi_id")->first();

        $master_gedungs = Gedung::whereRaw("is_active = 1")->get();
        $master_gedung_choosed = Gedung::whereRaw("id = $maintenance_header->gedung_id")->first();

        $master_ruangans = Ruangan::whereRaw("is_active = 1")->get();
        $master_ruangan_choosed = Ruangan::whereRaw("id = $maintenance_header->ruangan_id")->first();

        $master_alats = Alat::whereRaw("is_active = 1")->get();
        $master_alat_choosed = Alat::whereRaw("id = $maintenance_header->alat_id")->first();

        return Inertia::render('GASheet/MaintenanceHeadSheet/Edit', [
            'app_url' => env('APP_URL'),
            'maintenance_header' => $maintenance_header,
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
            'maintenance_tx' => $maintenance_tx,
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


        $maintenance = Maintenance::find($id);
        $maintenance->pic_name = $request->pic_name;
        $maintenance->pic_phone = $request->pic_phone;
        $maintenance->pic_email = $request->pic_email;
        $maintenance->pic_nip = $request->pic_nip;
        $maintenance->user_id = $user_id;
        $maintenance->company_name = $request->company_name;
        $maintenance->departement_name = $request->departement_name;
        // if($request->is_active){

        //     $Pic->is_active = 1;
        // } else {
        //     $Pic->is_active = 0;
        // }

        $maintenance->save();
        

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


 
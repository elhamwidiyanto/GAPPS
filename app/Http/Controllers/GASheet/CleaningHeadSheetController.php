<?php
namespace App\Http\Controllers\GASheet;
use App\Http\Controllers\Controller;
use App\Models\GASheet\Simbol_kondisi;
use App\Models\GASheet\Alat;
use App\Models\GASheet\Pic;
use App\Models\GASheet\Cleaning_tx;
use App\Models\GASheet\Cleaning_sheet;
use App\Models\GASheet\Cleaning_tx_history;
use App\Models\GASheet\Lokasi;
use App\Models\GASheet\Gedung;
use App\Models\GASheet\Ruangan;
use App\Models\GASheet\Cleaning_sheet_history;
use DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Carbon\Carbon;
class CleaningHeadSheetController extends Controller{
    public $timestamps = false;
    public function __construct()
    {
        $this->middleware('can:cleaning_head list', ['only' => ['index', 'show']]);
        $this->middleware('can:cleaning_head create', ['only' => ['create', 'store']]);
        $this->middleware('can:cleaning_head edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:cleaning_head delete', ['only' => ['destroy']]);
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
        $cleaning_header = Cleaning_sheet::get();

        return Inertia::render('GASheet/CleaningHeadSheet/Index', [
            'app_url' => env('APP_URL'),
            'master_lokasis' => $master_lokasis,
            'user_id' => $user_id,
        ]);
    }

    public function history()
    {
        $user_id = auth()->user()->id;

        return Inertia::render('GASheet/CleaningHeadSheet/History', [
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
        // $query = Cleaning_tx::selectRaw("cleaning_tx.*, lara_users.name AS user_name")->join("lara_users","lara_users.id","=","cleaning_tx.user_id");
        // if (request()->has('search')) {
        //     $query->where(function($subquery){
        //         $subquery->where('lokasi_name', 'LIKE', '%'.request()->input('search').'%')
        //         ->orWhere('user_name', 'LIKE', '%'.request()->input('search').'%');
        //     });
        // }
        $query = Cleaning_tx::selectRaw("cleaning_tx.*, lara_users.name AS user_name")->join("lara_users","lara_users.id","=","cleaning_tx.user_id");
        if (request()->has('search')) {
            $query->where(function($subquery){
                $subquery->where('cleaning_tx.lokasi_name', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('lara_users.name', 'LIKE', '%'.request()->input('search').'%');
            });
        }
        if (request()->has('year')) {
            $year = request()->input('year');
            $query->whereYear('cleaning_tx.date', $year); 
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
    public function status(Request $request) {
        $cleaning_header = Cleaning_sheet::find($request->id);
        $user_id = auth()->user()->id;
        if (!$cleaning_header) {
            return response()->json([
                'message' => 'Cleaning header not found',
            ], 404);
        }

        $cleaning_header->reason_reject = (!empty($request->reason_reject)) ? $request->reason_reject : null;
        $cleaning_header->status = $request->status;
        $cleaning_header->user_update_id = $user_id;
        $cleaning_header->save(); 
        // dd($cleaning_header->reason_reject);
        $cleaning_tx = cleaning_tx::whereRaw("cleaning_header_id = ".$cleaning_header['id'])->get();
        foreach ($cleaning_tx as $key => $value) {
            $cleaning_tx_update = cleaning_tx::find($cleaning_tx[$key]);
            $cleaning_tx_update->status = $request->status;
            $cleaning_tx_update->user_update_id = $user_id;
            $cleaning_history = new cleaning_tx_history();
            $cleaning_history->history_status = (!empty($cleaning_header->reason_reject)) ? 3 : 2;
            $cleaning_history->cleaning_header_id = $cleaning_header['id'];
            $cleaning_history->description = $value['description'];
            $cleaning_history->cleaning_tx_id = $value->id;
            $cleaning_history->reason_reject = (!empty($request->reason_reject)) ? $request->reason_reject : null;
            $cleaning_history->date = $value->date;
             $cleaning_history->is_checked = $value['is_checked'];
             $cleaning_history->user_update_id = $user_id;
             $cleaning_history->alat_id = $value['alat_id'];
             $cleaning_history->alat_name = $value['alat_name'];
             $cleaning_history->simbol_kondisi_id = $value['simbol_kondisi_id'];
             $cleaning_history->simbol_kondisi_name = $value['simbol_kondisi_name'];
             $cleaning_history->status = $request->status;
             $cleaning_history->user_id = auth()->user()->id;
             $cleaning_history->user_nip = auth()->user()->nip;
             $cleaning_history->user_phone = auth()->user()->phone;
             $cleaning_history->user_name = auth()->user()->name;
             $cleaning_history->company_id = auth()->user()->company_id;
             $cleaning_history->company_name = auth()->user()->company_name;
             $cleaning_history->department_name = auth()->user()->department_name;
             $cleaning_history->department_id = auth()->user()->department_id;
             $cleaning_history->pic_id = $cleaning_header->pic_id;
             $cleaning_history->pic_name = $cleaning_header->pic_name;
             $cleaning_history->pic_nip = $cleaning_header->pic_nip;
             $cleaning_history->pic_email = $cleaning_header->pic_email;
             $cleaning_history->pic_phone = $cleaning_header->pic_phone;
             $cleaning_history->lokasi_id = $value->lokasi_id;
             $cleaning_history->lokasi_name = $value->lokasi_name;
             $cleaning_history->gedung_id = $value->gedung_id;
             $cleaning_history->gedung_name = $value->gedung_name;
             $cleaning_history->ruangan_id = $value->ruangan_id;
             $cleaning_history->ruangan_name = $value->ruangan_name;
             $cleaning_history->save();
        }
    

        return response()->json([
            'message' => 'Status updated successfully',
            'cleaning_header' => $cleaning_header
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
        $query_get_user_info = Cleaning_tx::join('lara_users', 'lara_users.id', '=', 'cleaning_tx.user_id')
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
                $cleaning = new Cleaning_tx();
                $cleaning->date = Carbon::now();
                $cleaning->alat_name = $alat_names[$index];
                $cleaning->alat_id = $alat_ids[$index]; 
                $cleaning->simbol_kondisi_name = $simbol_names[$index]; 
                $cleaning->simbol_kondisi_id = $simbol_ids[$index]; 
                $cleaning->description = $request->description[$index]; 
                $cleaning->pic_id = $pic['id'];
                $cleaning->pic_nip = $pic['pic_nip'];
                $cleaning->pic_name = $pic['pic_name'];
                $cleaning->pic_email = $pic['pic_email'];
                $cleaning->pic_phone = $pic['pic_phone'];
                $cleaning->user_id = $user_id;
                $cleaning->status = 1;
                $cleaning->is_checked = 1;
                $cleaning->save();
                $cleaning_tx_history = new Cleaning_tx_history();
                $cleaning_tx_history->date = Carbon::now();
                $cleaning_tx_history->alat_name = $alat_names[$index];
                $cleaning_tx_history->alat_id = $alat_ids[$index]; 
                $cleaning_tx_history->simbol_kondisi_name = $simbol_names[$index]; 
                $cleaning_tx_history->simbol_kondisi_id = $simbol_ids[$index]; 
                $cleaning_tx_history->description = $request->description[$index]; 
                $cleaning_tx_history->pic_id = $pic['id'];
                $cleaning_tx_history->pic_nip = $pic['pic_nip'];
                $cleaning_tx_history->pic_name = $pic['pic_name'];
                $cleaning_tx_history->pic_email = $pic['pic_email'];
                $cleaning_tx_history->pic_phone = $pic['pic_phone'];
                $cleaning_tx_history->user_id = $user_id;
                $cleaning_tx_history->status = 1;
                $cleaning_tx_history->is_checked = 1;
                $cleaning_tx_history->save();
            } 
        }
    
        // redirect
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
        $cleaning_header = cleaning_sheet::find($id);

        $cleaning_tx = cleaning_tx::whereRaw("cleaning_header_id = $cleaning_header->id")->get();

        $simbol_kondisi_id_arr = [];

        foreach($cleaning_tx as $key => $value){
            $simbol_kondisi_id_arr[] = [
                'id' => $value->simbol_kondisi_id,
                'name' => $value->simbol_kondisi_name,
            ];
        }

        $master_simbol_kondisis = Simbol_kondisi::whereRaw("is_active = 1 && type = 2")->get();

        $query_get_pic = Pic::whereRaw("is_active = 1")->get();
        $query_get_pic_choosed = Pic::whereRaw("id = $cleaning_header->pic_id")->first();

        $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
        $master_lokasi_choosed = Lokasi::whereRaw("id = $cleaning_header->lokasi_id")->first();

        $master_gedungs = Gedung::whereRaw("is_active = 1")->get();
        $master_gedung_choosed = Gedung::whereRaw("id = $cleaning_header->gedung_id")->first();

        $master_ruangans = Ruangan::whereRaw("is_active = 1")->get();
        $master_ruangan_choosed = Ruangan::whereRaw("id = $cleaning_header->ruangan_id")->first();

        $master_alats = Alat::whereRaw("is_active = 1")->get();

        return Inertia::render('GASheet/CleaningHeadSheet/Edit', [
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
        ]);
    }
    // public function edit(string $id)
    // {
    //     //
    //     $cleaning_header = Cleaning_sheet::find($id);

    //     $cleaning_tx = Cleaning_tx::whereRaw("cleaning_header_id = $cleaning_header->id")->get();

    //     $simbol_kondisi_id_arr = [];

    //     foreach($cleaning_tx as $key => $value){
    //         $simbol_kondisi_id_arr[] = [
    //             'id' => $value->simbol_kondisi_id,
    //             'name' => $value->simbol_kondisi_name,
    //         ];
    //     }

    //     $master_simbol_kondisis = Simbol_kondisi::whereRaw("is_active = 1 && type = 1")->get();

    //     $query_get_pic = Pic::whereRaw("is_active = 1")->get();
    //     $query_get_pic_choosed = Pic::whereRaw("id = $cleaning_header->pic_id")->first();

    //     $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
    //     $master_lokasi_choosed = Lokasi::whereRaw("id = $cleaning_header->lokasi_id")->first();

    //     $master_gedungs = Gedung::whereRaw("is_active = 1")->get();
    //     $master_gedung_choosed = Gedung::whereRaw("id = $cleaning_header->gedung_id")->first();
 
    //     $master_ruangans = Ruangan::whereRaw("is_active = 1")->get();
    //     $master_ruangan_choosed = Ruangan::whereRaw("id = $cleaning_header->ruangan_id")->first();

    //     return Inertia::render('GASheet/CleaningHeadSheet/Edit', [
    //         'app_url' => env('APP_URL'),
    //         'cleaning_header' => $cleaning_header,
    //         'master_simbol_kondisis' => $master_simbol_kondisis,
    //         'master_pics'  => $query_get_pic,
    //         'simbol_kondisi_id_arr' => $simbol_kondisi_id_arr,
    //         'query_get_pic_choosed' => $query_get_pic_choosed,
    //         'master_lokasis' => $master_lokasis,
    //         'master_lokasi_choosed' => $master_lokasi_choosed,
    //         'master_gedungs' => $master_gedungs,
    //         'master_gedung_choosed' => $master_gedung_choosed,
    //         'master_ruangans' => $master_ruangans,
    //         'master_ruangan_choosed' => $master_ruangan_choosed,
    //         'cleaning_tx' => $cleaning_tx
    //     ]);
        
    // } 
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
         // Temukan cleaning_header dengan ID yang diberikan
        $cleaning_header = Cleaning_sheet::find($request->id);

        // Periksa apakah cleaning_header ditemukan
        if (!$cleaning_header) {
            return response()->json([
                'message' => 'Cleaning header not found',
            ], 404);
        }

        // Perbarui status
        $cleaning_header->status = $request->status;
        $cleaning_header->save(); // Simpan perubahan

        // Kembalikan respons
        return response()->json([
            'message' => 'Status updated successfully',
            'cleaning_header' => $cleaning_header
        ]);  
     }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

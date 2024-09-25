<?php

namespace App\Http\Controllers\GASheet;

use App\Http\Controllers\Controller;

use App\Models\GASheet\Maintenance;
use App\Models\GASheet\Maintenance_sheet;
use App\Models\GASheet\Maintenance_tx;
use App\Models\GASheet\Simbol_kondisi;
use App\Models\GASheet\Alat;
use App\Models\GASheet\Lokasi;
use App\Models\GASheet\Gedung;
use App\Models\GASheet\Maintenance_history;
use App\Models\GASheet\Ruangan;
use App\Models\GASheet\Pic;
use App\Models\GASheet\Komponen;
 

use Illuminate\Support\Facades\DB;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use PDF;
use Inertia\Inertia;
use Carbon\Carbon;

class MasterMaintenanceController extends Controller{
    public $timestamps = false;

    public function __construct()
    {
        $this->middleware('can:master_maintenance list', ['only' => ['index', 'show']]);
        $this->middleware('can:master_maintenance create', ['only' => ['create', 'store']]);
        $this->middleware('can:master_maintenance edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:master_maintenance delete', ['only' => ['destroy']]);
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
        $dataGroup = Maintenance::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('gedung_id as gedung_id'),
            DB::raw('gedung_name as gedung_name'),
            DB::raw('user_id as user_id'),
            DB::raw('user_name as user_name'),
            DB::raw('COUNT(*) as total')
        )
        ->where('is_checked', 1)
        ->groupBy(
            DB::raw('YEAR(created_at)'),
            'gedung_id',
            'gedung_name',
            'user_id',
            'user_name'
        )
        ->get();

        return Inertia::render('GASheet/Maintenance/Index', [
            'app_url' => env('APP_URL'),
            'user_id' => $user_id,
            'dataGroup' => $dataGroup,
        ]);    
    }

    public function index_group()
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
        $dataGroup = Maintenance::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('gedung_id as gedung_id'),
            DB::raw('gedung_name as gedung_name'),
            DB::raw('user_id as user_id'),
            DB::raw('user_name as user_name'),
            DB::raw('COUNT(*) as total')
        )
        ->where('is_checked', 1)
        ->groupBy(
            DB::raw('YEAR(created_at)'),
            'gedung_id',
            'gedung_name',
            'user_id',
            'user_name'
        )
        ->get(); 

        return Inertia::render('GASheet/Maintenance/IndexGroup', [
            'app_url' => env('APP_URL'),
            'user_id' => $user_id,
            'dataGroup' => $dataGroup,
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

        $query = maintenance_sheet::selectRaw("*");

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
        return Inertia::render('GASheet/Maintenance/Scan', [
            'app_url' => env('APP_URL'),
        ]);
    }

    public function check_create_today(Request $request){
        $today = Carbon::today();
        $maintenanceChecks = Maintenance_sheet::where('alat_id', $request->id_alat)
            ->whereDate('created_at', $today)
            ->first();
    
        if($maintenanceChecks != null){
            return response()->json([
                'error' => 'Alat ini sudah di cek oleh ' . $maintenanceChecks->user_name . ' hari ini!',
            ], 400);
        } else {
            return response()->json([
                'success' => 'Maintenance berhasil ditambahkan!',
            ]);
        }
    }
    public function get_komponen_simbol_kondisi(Request $request)
    {
        $location = $request->value_location;
        $gedung = $request->value_gedung;
        $ruangan = $request->value_ruangan;
        $alat = $request->value_alat;
        // dd($alat);

        $query_get_komponen = Komponen::whereRaw("alat_id = ".$alat['id'] ." AND type = " .'2')->get();
        // dd($query_get_komponen);
        $query_get_simbol_kondisi = Simbol_kondisi::whereRaw("alat_id = ".$alat['id'] ." AND type = " ."2  AND is_active = 1")->get();
        
        foreach ($query_get_komponen as $key => $komponen) {
            $simbol_kondisi_ids[$key] = $komponen->default_simbol_id;
        }
        // dd($simbol_kondisi_ids);
        $query_get_default_simbol = collect();

        foreach ($simbol_kondisi_ids as $id) {
            $simbol = Simbol_kondisi::where('id', $id)
                ->where('type', 2)
                ->where('is_active', 1)
                ->first();
            if ($simbol) {
                $query_get_default_simbol->push($simbol);
            }
        }
        // dd($query_get_default_simbol);
        return response()->json([
            'success' => true,
            'data_komponen'    => $query_get_komponen,
            'data_simbol_kondisi'    => $query_get_simbol_kondisi,
            'default_simbol_kondisi' => $query_get_default_simbol,
            
        ]);
    } 
    public function history($id){
        $maintenance_tx_history = Maintenance_history::where("maintenance_header_id", $id)
            ->selectRaw("maintenance_tx_history.*, lara_users.name AS user_update_name")
            ->join("lara_users", "lara_users.id", "=", "maintenance_tx_history.user_update_id")
            ->get();        
        // dd($maintenance_tx_history);
        $user_id = auth()->user()->id; 
        // dd($maintenance_tx_history);
        return Inertia::render('GASheet/MaintenanceHeadSheet/History', [
            'maintenance_histories' => $maintenance_tx_history,
            'user_login' => $user_id,
        ]);
    }

    public function report($id){
        $maintenanceHeader_report = Maintenance_sheet::find($id, ['lokasi_id', 'gedung_id', 'ruangan_id', 'alat_id', 'created_at']);

        $maintenanceHeaders = Maintenance_sheet::where('lokasi_id', $maintenanceHeader_report->lokasi_id)
            ->where('gedung_id', $maintenanceHeader_report->gedung_id)
            ->where('ruangan_id', $maintenanceHeader_report->ruangan_id)
            ->where('alat_id', $maintenanceHeader_report->alat_id)
            ->where('status', 2)
            ->whereYear('created_at', $maintenanceHeader_report->created_at->year)
            ->whereMonth('created_at', $maintenanceHeader_report->created_at->month)
            ->get('id');
        $ids = $maintenanceHeaders->pluck('id')->toArray();
        // dd($ids);
        $maintenanceTxRecords = Maintenance::whereIn('maintenance_header_id', $ids)
            ->get(['created_at', 'is_checked', 'komponen_name']);
        // dd($maintenanceTxRecords);
        //  dd($maintenanceTxRecords);
        $result = $maintenanceTxRecords->map(function($record) {
            return [
                'date' => (int) Carbon::parse($record->created_at)->format('j'),
                'is_checked' => $record->is_checked,
                'komponen_name' => $record->komponen_name
            ];
        });
        // dd($result);
        // return Inertia::render('GASheet/Maintenance/Report', [
        //     'result' => $result
        // ]);
        // $pdf = PDF::loadView('myPDF', $result);
        // return $pdf->download('itsolutionstuff.pdf');
    }

    public function get_komponen_simbol_kondisi_qr(Request $request)
    {
        $alat = $request->value_alat;
        $alat_id = $request->value_alat_id;

        $query_get_komponen = Komponen::whereRaw("alat_id = ".$alat['id'] ." AND type = " .'2')->get();

        $query_get_simbol_kondisi = Simbol_kondisi::whereRaw("alat_id = ".$alat['id'] ." AND type = " ."2  AND is_active = 1" )->get();
        // dd($query_get_simbol_kondisi);
        foreach ($query_get_komponen as $key => $komponen) {
            $simbol_kondisi_ids[$key] = $komponen->default_simbol_id;
        }
        $query_get_default_simbol = collect();

        foreach ($simbol_kondisi_ids as $id) {
            $simbol = Simbol_kondisi::where('id', $id)
                ->where('type', 2)
                ->where('is_active', 1)
                ->first();
            if ($simbol) {
                $query_get_default_simbol->push($simbol);
            }
        }
        
        return response()->json([
            'success' => true,
            'data_komponen'    => $query_get_komponen,
            'data_simbol_kondisi'    => $query_get_simbol_kondisi,
            'default_simbol_kondisi' => $query_get_default_simbol,
            
        ]);
    }
    public function index_detail(string $id){
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

        return Inertia::render('GASheet/Maintenance/Edit', [
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
            'maintenance_tx' => $maintenance_tx
        ]);
    }

    public function create()
    {
        $master_simbol_kondisis = Simbol_kondisi::whereRaw("is_active = 1 && type = 2")->get();
        $master_alats = Alat::whereRaw("is_active = 1 && type = 2")->get();
        $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
        $master_gedungs = Gedung::whereRaw("is_active = 1")->get();
        $master_ruangans = Ruangan::whereRaw("is_active = 1")->get();
        $master_komponen = Komponen::whereRaw("is_active = 1 AND type = 2")->get();
        $query_get_pic = Pic::whereRaw("is_active = 1 AND type = 2")->get();
        return Inertia::render('GASheet/Maintenance/Create', [
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
        $master_simbol_kondisis = Simbol_kondisi::whereRaw("is_active = 1 && type = 1")->get();
        $master_pics = Pic::whereRaw("is_active = 1 AND type = 2")->get();
        // $master_alats = Alat::whereRaw("is_active = 1 && type = 2")->get();
        $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
        $master_gedungs = Gedung::whereRaw("is_active = 1")->get();
        $master_ruangans = Ruangan::whereRaw("is_active = 1")->get();
        return Inertia::render('GASheet/Maintenance/Create', [
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

        $maintenance_header = new Maintenance_sheet();
        $maintenance_header->date = date("Y-m-d H:i:s");
        $maintenance_header->status = 1;
        $maintenance_header->user_update_id = $user_id;
        $maintenance_header->user_id = auth()->user()->id;
        $maintenance_header->user_nip = auth()->user()->nip;
        $maintenance_header->user_phone = auth()->user()->phone;
        $maintenance_header->user_name = auth()->user()->name;
        $maintenance_header->company_id = auth()->user()->company_id;
        $maintenance_header->company_name = auth()->user()->company_name;
        $maintenance_header->department_name = auth()->user()->department_name;
        $maintenance_header->department_id = auth()->user()->department_id;
        $maintenance_header->pic_id = $request->pic['id'];
        $maintenance_header->pic_name = $request->pic['pic_name'];
        $maintenance_header->pic_nip = $request->pic['pic_nip'];
        $maintenance_header->pic_email = $request->pic['pic_email'];
        $maintenance_header->pic_phone = $request->pic['pic_phone'];
        $maintenance_header->lokasi_id = $request->location['id'];
        $maintenance_header->lokasi_name = $request->location['name'];
        $maintenance_header->alat_id = $request->master_alats['id'];
        $maintenance_header->alat_name = $request->master_alats['name'];
        $maintenance_header->serial_number = $request->master_alats['serial_number'];
        $maintenance_header->gedung_id = $request->gedung['id'];
        $maintenance_header->gedung_name = $request->gedung['name'];
        $maintenance_header->ruangan_id = $request->ruangan['id'];
        $maintenance_header->ruangan_name = $request->ruangan['name'];
        $maintenance_header->save();


        foreach ($request->master_komponens as $key => $value) {
            $maintenance_tx = new maintenance();
            $maintenance_tx->maintenance_header_id = $maintenance_header->id;
            $maintenance_tx->user_update_id = $user_id;
            $maintenance_tx->date = $maintenance_header->date;
            $maintenance_tx->is_checked = (!empty($request->is_checked[$key])) ? 1 : 0;
            $maintenance_tx->description = (!empty($request->description[$key])) ? $request->description[$key] : null;
            $maintenance_tx->alat_id = $request->master_alats['id'];
            $maintenance_tx->alat_name = $request->master_alats['name'];
            $maintenance_tx->serial_number = $request->master_alats['serial_number'];
            $maintenance_tx->komponen_id = $value['id'];
            $maintenance_tx->komponen_name = $value['name'];
            $maintenance_tx->simbol_kondisi_id = (!empty($request->simbol_name[$key]['id'])) ? $request->simbol_name[$key]['id'] : null;
            $maintenance_tx->simbol_kondisi_name = (!empty($request->simbol_name[$key]['name'])) ? $request->simbol_name[$key]['name'] : null;
            $maintenance_tx->status = $maintenance_header->status;
            $maintenance_tx->user_id = auth()->user()->id;
            $maintenance_tx->user_nip = auth()->user()->nip;
            $maintenance_tx->user_phone = auth()->user()->phone;
            $maintenance_tx->user_name = auth()->user()->name;
            $maintenance_tx->company_id = auth()->user()->company_id;
            $maintenance_tx->company_name = auth()->user()->company_name;
            $maintenance_tx->department_name = auth()->user()->department_name;
            $maintenance_tx->department_id = auth()->user()->department_id;
            $maintenance_tx->pic_id = $request->pic['id'];
            $maintenance_tx->pic_name = $request->pic['pic_name'];
            $maintenance_tx->pic_nip = $request->pic['pic_nip'];
            $maintenance_tx->pic_email = $request->pic['pic_email'];
            $maintenance_tx->pic_phone = $request->pic['pic_phone'];
            $maintenance_tx->lokasi_id = $request->location['id'];
            $maintenance_tx->lokasi_name = $request->location['name'];
            $maintenance_tx->gedung_id = $request->gedung['id'];
            $maintenance_tx->gedung_name = $request->gedung['name'];
            $maintenance_tx->ruangan_id = $request->ruangan['id'];
            $maintenance_tx->ruangan_name = $request->ruangan['name'];
            $maintenance_tx->save();

            $maintenance_history = new maintenance_history();
            $maintenance_history->history_status = 0;
            $maintenance_history->maintenance_header_id = $maintenance_header->id;
            $maintenance_history->maintenance_tx_id = $maintenance_tx->id;
            $maintenance_history->user_update_id = $user_id;
            $maintenance_history->date = $maintenance_header->date;
            $maintenance_history->is_checked = (!empty($request->is_checked[$key])) ? 1 : 0;
            $maintenance_history->description = (!empty($request->description[$key])) ? $request->description[$key] : null;
            $maintenance_history->alat_id = $request->master_alats['id'];
            $maintenance_history->alat_name = $request->master_alats['name'];
            $maintenance_history->serial_number = $request->master_alats['serial_number'];
            $maintenance_history->komponen_id = $value['id'];
            $maintenance_history->komponen_name = $value['name'];
            $maintenance_history->simbol_kondisi_id = (!empty($request->simbol_name[$key]['id'])) ? $request->simbol_name[$key]['id'] : null;
            $maintenance_history->simbol_kondisi_name = (!empty($request->simbol_name[$key]['name'])) ? $request->simbol_name[$key]['name'] : null;
            $maintenance_history->status = $maintenance_header->status;
            $maintenance_history->user_id = auth()->user()->id;
            $maintenance_history->user_nip = auth()->user()->nip;
            $maintenance_history->user_phone = auth()->user()->phone;
            $maintenance_history->user_name = auth()->user()->name;
            $maintenance_history->company_id = auth()->user()->company_id;
            $maintenance_history->company_name = auth()->user()->company_name;
            $maintenance_history->department_name = auth()->user()->department_name;
            $maintenance_history->department_id = auth()->user()->department_id;
            $maintenance_history->pic_id = $request->pic['id'];
            $maintenance_history->pic_name = $request->pic['pic_name'];
            $maintenance_history->pic_nip = $request->pic['pic_nip'];
            $maintenance_history->pic_email = $request->pic['pic_email'];
            $maintenance_history->pic_phone = $request->pic['pic_phone'];
            $maintenance_history->lokasi_id = $request->location['id'];
            $maintenance_history->lokasi_name = $request->location['name'];
            $maintenance_history->gedung_id = $request->gedung['id'];
            $maintenance_history->gedung_name = $request->gedung['name'];
            $maintenance_history->ruangan_id = $request->ruangan['id'];
            $maintenance_history->ruangan_name = $request->ruangan['name'];
            $maintenance_history->save();
        }
        
        return redirect('/ga_sheet/maintenance');

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

        return Inertia::render('GASheet/Maintenance/Edit', [
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
            'status' =>$maintenance_header->status,
            // dd($maintenance_header->status)
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

  
     public function update(Request $request, string $id)   
     {
         //
         $maintenance_header = maintenance_sheet::find($id);
         $user_id = auth()->user()->id;
         // $maintenance_header->date = date("Y-m-d H:i:s");
         
         $maintenance_header->status = $request->status;
 
         // $maintenance_header->user_id = auth()->user()->id;
         // $maintenance_header->user_nip = auth()->user()->nip;
         // $maintenance_header->user_phone = auth()->user()->phone;
         // $maintenance_header->user_name = auth()->user()->name;
         // $maintenance_header->company_id = auth()->user()->company_id;
         // $maintenance_header->company_name = auth()->user()->company_name;
         // $maintenance_header->department_name = auth()->user()->department_name;
         // $maintenance_header->department_id = auth()->user()->department_id;
 
         $maintenance_header->pic_id = $request->pic['id'];
         $maintenance_header->pic_name = $request->pic['pic_name'];
         $maintenance_header->pic_nip = $request->pic['pic_nip'];
         $maintenance_header->pic_email = $request->pic['pic_email'];
         $maintenance_header->pic_phone = $request->pic['pic_phone'];
         $maintenance_header->user_update_id = $user_id;
 
         $maintenance_header->save();
 
         maintenance::whereRaw("maintenance_header_id = $maintenance_header->id")->delete();
 
         foreach ($request->maintenance_tx as $key => $value) {
 
             $maintenance_tx = new maintenance();
             $maintenance_tx->id = $value['id'];
             $maintenance_tx->maintenance_header_id = $maintenance_header->id;
             $maintenance_tx->date = $maintenance_header->date;
             $maintenance_tx->user_update_id = $user_id;
             $maintenance_tx->is_checked = ($request->is_checked[$key]) ? 1 : 0;
             $maintenance_tx->description = (!empty($request->description[$key])) ? $request->description[$key] : null;
             $maintenance_tx->alat_id = $value['alat_id'];
             $maintenance_tx->alat_name = $value['alat_name'];
             $maintenance_tx->serial_number = $value['serial_number'];
             $maintenance_tx->simbol_kondisi_id = (!empty($request->simbol_name[$key]['id'])) ? $request->simbol_name[$key]['id'] : null;
             $maintenance_tx->simbol_kondisi_name = (!empty($request->simbol_name[$key]['name'])) ? $request->simbol_name[$key]['name'] : null;
             $maintenance_tx->status = $maintenance_header->status;
             $maintenance_tx->user_id = auth()->user()->id;
             $maintenance_tx->user_nip = auth()->user()->nip;
             $maintenance_tx->user_phone = auth()->user()->phone;
             $maintenance_tx->user_name = auth()->user()->name;
             $maintenance_tx->company_id = auth()->user()->company_id;
             $maintenance_tx->company_name = auth()->user()->company_name;
             $maintenance_tx->department_name = auth()->user()->department_name;
             $maintenance_tx->department_id = auth()->user()->department_id;
             $maintenance_tx->pic_id = $request->pic['id'];
             $maintenance_tx->pic_name = $request->pic['pic_name'];
             $maintenance_tx->pic_nip = $request->pic['pic_nip'];
             $maintenance_tx->pic_email = $request->pic['pic_email'];
             $maintenance_tx->pic_phone = $request->pic['pic_phone'];
             $maintenance_tx->lokasi_id = $request->location['id'];
             $maintenance_tx->lokasi_name = $request->location['name'];
             $maintenance_tx->gedung_id = $request->gedung['id'];
             $maintenance_tx->gedung_name = $request->gedung['name'];
             $maintenance_tx->ruangan_id = $request->ruangan['id'];
             $maintenance_tx->ruangan_name = $request->ruangan['name'];
             $maintenance_tx->komponen_id = $value['komponen_id'];
             $maintenance_tx->komponen_name = $value['komponen_name'];
             $maintenance_tx->save();

             $maintenance_history = new maintenance_history();
             $maintenance_history->history_status = $request->status;
             $maintenance_history->maintenance_tx_id = $maintenance_tx->id;
             $maintenance_history->maintenance_header_id = $maintenance_header->id;
             $maintenance_history->date = $maintenance_tx->date;
             $maintenance_history->user_update_id = $user_id;
             $maintenance_history->is_checked = ($request->is_checked[$key]) ? 1 : 0;
             $maintenance_history->description = (!empty($request->description[$key])) ? $request->description[$key] : null;
             $maintenance_history->alat_id = $value['alat_id'];
             $maintenance_history->alat_name = $value['alat_name'];
             $maintenance_history->alat_name = $value['alat_name'];
             $maintenance_history->simbol_kondisi_id = (!empty($request->simbol_name[$key]['id'])) ? $request->simbol_name[$key]['id'] : null;
             $maintenance_history->simbol_kondisi_name = (!empty($request->simbol_name[$key]['name'])) ? $request->simbol_name[$key]['name'] : null;
             $maintenance_history->status = $maintenance_header->status;
             $maintenance_history->user_id = auth()->user()->id;
             $maintenance_history->user_nip = auth()->user()->nip;
             $maintenance_history->user_phone = auth()->user()->phone;
             $maintenance_history->user_name = auth()->user()->name;
             $maintenance_history->company_id = auth()->user()->company_id;
             $maintenance_history->company_name = auth()->user()->company_name;
             $maintenance_history->department_name = auth()->user()->department_name;
             $maintenance_history->department_id = auth()->user()->department_id;
             $maintenance_history->pic_id = $request->pic['id'];
             $maintenance_history->pic_name = $request->pic['pic_name'];
             $maintenance_history->pic_nip = $request->pic['pic_nip'];
             $maintenance_history->pic_email = $request->pic['pic_email'];
             $maintenance_history->pic_phone = $request->pic['pic_phone'];
             $maintenance_history->lokasi_id = $request->location['id'];
             $maintenance_history->lokasi_name = $request->location['name'];
             $maintenance_history->gedung_id = $request->gedung['id'];
             $maintenance_history->gedung_name = $request->gedung['name'];
             $maintenance_history->ruangan_id = $request->ruangan['id'];
             $maintenance_history->ruangan_name = $request->ruangan['name'];
             $maintenance_history->komponen_id = $value['komponen_id'];
             $maintenance_history->komponen_name = $value['komponen_name'];
             $maintenance_history->save();
         }
         
         return redirect('/ga_sheet/maintenance');
         
     }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}



<?php

namespace App\Http\Controllers\GASheet;

use App\Http\Controllers\Controller;

use App\Models\GASheet\Master_type;
use App\Models\GASheet\Simbol_kondisi;
use App\Models\GASheet\Alat;
use App\Models\GASheet\Lokasi;
use App\Models\GASheet\Gedung;
use App\Models\GASheet\Ruangan;
use App\Models\GASheet\Sheet_role;
use DB;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class MasterSimbolKondisiController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:master_simbol_kondisi list', ['only' => ['index', 'show']]);
        $this->middleware('can:master_simbol_kondisi create', ['only' => ['create', 'store']]);
        $this->middleware('can:master_simbol_kondisi edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:master_simbol_kondisi delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
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

        return Inertia::render('GASheet/SimbolKondisi/Index', [
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

        $query = Simbol_kondisi::selectRaw("simbol_kondisi.*, lara_users.name AS user_name")->join("lara_users","lara_users.id","=","simbol_kondisi.user_id");

        if (request()->has('search')) {
            $query->where(function($subquery){
                $subquery->where('simbol_kondisi.code', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('simbol_kondisi.name', 'LIKE', '%'.request()->input('search').'%');
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

        if (request('field')) {
            $query->orderBy(request('field'), request('direction'))->orderBy('simbol_kondisi.created_at', request('direction'));
        } else {
            $query->orderBy("simbol_kondisi.id", "desc");
        }

        $data = $query->paginate(5)->onEachSide(2);
        
        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }

    public function get_companies(Request $request)
    {
        $nip = auth()->user()->nip;
        
        $companies = Param_company::whereRaw('is_active = 1')->get();

        if($companies) {
            return response()->json([
                'success' => true,
                'data'    => $companies  
            ]);
        }

        return response()->json([
            'success' => false,
            'data'    => null  
        ]);
    }

    public function check_duplicate(Request $request)
    {
        if(!empty($request->master_pihak_lain_id)){

            $master_pihak_lain = Sirkuler_master_pihak_lain::whereRaw("company_id = $request->company_id AND id != $request->master_pihak_lain_id")->first();
        
        } else {

            $master_pihak_lain = Sirkuler_master_pihak_lain::whereRaw("company_id = $request->company_id")->first();
        }

        if(!empty($master_pihak_lain)) {
            $is_duplicate = 1;
        } else {
            $is_duplicate = 0;
        }

        return response()->json([
            'success' => true,
            'data'    => $is_duplicate  
        ]);
    }

    public function check_duplicate_code(Request $request)
    {
        $code = strtoupper($request->code);

        if(!empty($request->master_simbol_kondisi_code)){

            $master_simbol_kondisi = Simbol_kondisi::whereRaw("code LIKE '%$code%' AND id != $request->master_simbol_kondisi_code")->first();
        
        } else {

            $master_simbol_kondisi = Simbol_kondisi::whereRaw("code LIKE '%$code%'")->first();
        }

        if(!empty($master_simbol_kondisi)) {
            $is_duplicate = 1;
        } else {
            $is_duplicate = 0;
        }

        return response()->json([
            'success' => true,
            'data'    => $is_duplicate  
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $user_id = auth()->user()->id;

        $my_role = Sheet_role::whereRaw("user_id = $user_id AND is_active = 1")->get();

        foreach ($my_role as $key => $value) {
            $type_id[] = $value->type_id;
        }

        $master_types = Master_type::whereRaw("is_active = 1")
        ->whereIn("id", $type_id)->get();

        return Inertia::render('GASheet/SimbolKondisi/Create', [
            'app_url' => env('APP_URL'),
            'master_types' => $master_types
        ]);
    }

    public function get_alat(Request $request){
        $sheet = $request->sheet['id'];
        $alats = Alat::where('type', $sheet)
            ->get(['id', 'name', 'serial_number']);
        return response()->json([
            'alats' => $alats,
        ]);
    }
    public function get_lokasi(){
        $lokasis = Lokasi::where('is_active', 1)
            ->get();
        return response()->json([
                'lokasis' => $lokasis,
            ]);
    }
    public function get_gedung(Request $request){
        $lokasi_id = $request->lokasi_id;
        $gedungs = Gedung::where('id_lokasi', $lokasi_id)
            ->where('is_active', 1)
            ->get();
        return response()->json([
            'gedungs' => $gedungs,
        ]);
    }
    public function get_ruangan(Request $request){
        $gedung_id = $request->gedung_id;
        $ruangans = Ruangan::where('id_gedung', $gedung_id)
        ->where('is_active', 1)
        ->get();
        return response()->json([
            'ruangans' => $ruangans,
        ]);
    }

    public function get_alat_cleaning(Request $request){
        $lokasi_id = $request->lokasi_id;
        $gedung_id = $request->gedung_id;
        $ruangan_id = $request->ruangan_id;

        $alats = Alat::where('location_id', $lokasi_id)
            ->where('gedung_id', $gedung_id)
            ->where('ruangan_id', $ruangan_id)
            ->where('type', 1)
            ->get();

        return response()->json([
            'alats' => $alats,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $code = $request->code;
        $user_id = auth()->user()->id;
        $found = simbol_kondisi::where('code', $code)->count();
        if($found == 0)
        {
            $simbol_kondisi = new Simbol_kondisi();
            $simbol_kondisi->alat_id = $request->alat_id;
            $simbol_kondisi->code = $code;
            $simbol_kondisi->type = $request->type;
            $simbol_kondisi->name = $request->name;
            $simbol_kondisi->user_id = $user_id;
            $simbol_kondisi->location_id = (!empty($request->lokasi_id)) ? $request->lokasi_id : null;
            $simbol_kondisi->gedung_id = (!empty($request->gedung_id)) ? $request->gedung_id : null;
            $simbol_kondisi->ruangan_id = (!empty($request->ruangan_id)) ? $request->ruangan_id : null;
            if($request->is_active){

                $simbol_kondisi->is_active = 1;
            } else {
                $simbol_kondisi->is_active = 0;
            }
            $simbol_kondisi->save();
            $createStatus = 1;
            return response()->json([
                'status' => 'success',
                'message' => 'Simbol kondisi berhasil ditambahkan!',
            ]);
            // return redirect('/ga_sheet/simbol_kondisi');  
            
        }else{
            $createStatus = 0;
            // return redirect('/ga_sheet/simbol_kondisi/create'); 
            return response()->json([
                'status' => 'error',
                'message' => 'Code sudah digunakan!',
            ], 422);
        }
        
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
        $simbol_kondisi = Simbol_kondisi::find($id);

        $master_types = Master_type::whereRaw("is_active = 1")->get();
        $master_type = Master_type::whereRaw("id = $simbol_kondisi->type")->first();

        return Inertia::render('GASheet/SimbolKondisi/Edit', [
            'app_url' => env('APP_URL'),
            'simbol_kondisi' => $simbol_kondisi,
            'master_types' => $master_types,
            'master_type' => $master_type,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $user_id = auth()->user()->id;

        $simbol_kondisi = Simbol_kondisi::find($id);
        $simbol_kondisi->code = $request->code;
        $simbol_kondisi->name = $request->name;
        $simbol_kondisi->user_id = $user_id;

        if(!empty($request->type['id'])){
            
            $simbol_kondisi->type = $request->type['id'];
        }

        if($request->is_active){

            $simbol_kondisi->is_active = 1;
        } else {
            $simbol_kondisi->is_active = 0;
        }

        $simbol_kondisi->save();

        return redirect('/ga_sheet/simbol_kondisi');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(simbolkondisi $simbol_kondisi)
    {
        $simbolkondisi->delete();

        $this->storeLogActivities("user delete data pic", auth()->user()->id);
        return redirect()->route('/ga_sheet/simbol_kondisi')
                        ->with('message', __('Person in Charge deleted successfully'));
    }
}

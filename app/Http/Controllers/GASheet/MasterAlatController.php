<?php

namespace App\Http\Controllers\GASheet;

use App\Http\Controllers\Controller;

use App\Models\GASheet\Alat;
use App\Models\GASheet\Master_type;
use App\Models\GASheet\Lokasi;
use App\Models\GASheet\Gedung;
use App\Models\GASheet\Ruangan;
use App\Models\GASheet\Simbol_kondisi;
use App\Models\GASheet\Sheet_role;

use DB;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class MasterAlatController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:master_alat list', ['only' => ['index', 'show']]);
        $this->middleware('can:master_alat create', ['only' => ['create', 'store']]);
        $this->middleware('can:master_alat edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:master_alat delete', ['only' => ['destroy']]);
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

        return Inertia::render('GASheet/Alat/Index', [
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

        $query = Alat::selectRaw("alat.*, lara_users.name AS user_name")->join("lara_users","lara_users.id","=","alat.id");

        if (request()->has('search')) {
            $query->where(function($subquery){
                $subquery->where('alat.user_id', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('alat.name', 'LIKE', '%'.request()->input('search').'%');
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
            $query->orderBy(request('field'), request('direction'))->orderBy('alat.created_at', request('direction'));
        } else {
            $query->orderBy("alat.id", "desc");
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

        if(!empty($request->master_pihak_lain_id)){

            $master_pihak_lain = Sirkuler_master_pihak_lain::whereRaw("code LIKE '%$code%' AND id != $request->master_pihak_lain_id")->first();
        
        } else {

            $master_pihak_lain = Sirkuler_master_pihak_lain::whereRaw("code LIKE '%$code%'")->first();
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user_id = Auth()->user()->id;
        $my_role = Sheet_role::whereRaw("user_id = $user_id AND is_active = 1")->get();

        foreach ($my_role as $key => $value) {
            $type_id[] = $value->type_id;
        }

        $master_types = Master_type::whereRaw("is_active = 1")
        ->whereIn("id", $type_id)->get();
        return Inertia::render('GASheet/Alat/Create', [
            'app_url' => env('APP_URL'),
            'master_types' => $master_types
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

    public function get_default_simbol(Request $request){
        $location = $request->lokasi_id;
        $gedung = $request->gedung_id;
        $ruangan = $request->ruangan_id;
        // dd($alat);
        $simbol_kondisis = Simbol_kondisi::whereRaw("location_id = ".$location ." AND gedung_id = ".$gedung." AND ruangan_id = ".$ruangan." AND type  =" .'1')
            ->get();
        // dd($simbol_kondisis);
        return response()->json([
            'success' => true,
            'simbol_kondisis'    => $simbol_kondisis,  
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $user_id = auth()->user()->id;
        $alat= new Alat();
        $alat->user_id = $user_id;
        $alat->name = $request->name;
        $alat->type = $request->type_id;
        $alat->default_simbol_id = $request->default_simbol;
        $alat->location_id = (!empty($request->lokasi_id)) ? $request->lokasi_id : null;
        $alat->gedung_id = (!empty($request->gedung_id)) ? $request->gedung_id : null;
        $alat->ruangan_id = (!empty($request->ruangan_id)) ? $request->ruangan_id : null;
        if($request->is_active){

            $alat->is_active = 1;
        } else {
            $alat->is_active = 0;
        }

        $alat->save();

        //redirect
        return redirect('/ga_sheet/alat');
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
        $alat = Alat::find($id);

        return Inertia::render('GASheet/Alat/Edit', [
            'app_url' => env('APP_URL'),
            'alat' => $alat
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $user_id = auth()->user()->id;

        $alat = Alat::find($id);
        $alat->name = $request->name;
        $alat->user_id = $user_id;

        if($request->is_active){

            $alat->is_active = 1;
        } else {
            $alat->is_active = 0;
        }

        $alat->save();

        //redirect
        return redirect('/ga_sheet/alat');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

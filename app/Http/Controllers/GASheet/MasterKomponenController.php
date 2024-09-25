<?php

namespace App\Http\Controllers\GASheet;

use App\Http\Controllers\Controller;

use App\Models\GASheet\Komponen;
use App\Models\GASheet\Lokasi;
use App\Models\GASheet\Gedung;
use App\Models\GASheet\Ruangan;
use App\Models\GASheet\Alat;
use App\Models\GASheet\Master_type;
use App\Models\GASheet\Simbol_kondisi;
use App\Models\GASheet\Sheet_role;

use DB;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class MasterKomponenController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:master_komponen list', ['only' => ['index', 'show']]);
        $this->middleware('can:master_komponen create', ['only' => ['create', 'store']]);
        $this->middleware('can:master_komponen edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:master_komponen delete', ['only' => ['destroy']]);
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

        return Inertia::render('GASheet/MasterKomponen/Index', [
            'app_url' => env('APP_URL'),
            'user_id' => $user_id,
        ]);
    }

    public function json_index()
    {
        //
        $nip = auth()->user()->nip;
        $user_id = auth()->user()->id;
        $user_update_id = auth()->user()->id;
        $name = auth()->user()->name;
        
        $company_id = auth()->user()->company_id;
        $hasRoles = auth()->user()->getRoleNames();// Returns a collection

        $myRoles = [];
        foreach($hasRoles as $key => $value){
            $myRoles[] = $value;
        }

        $query = Komponen::selectRaw("master_komponen.*, lara_users.name AS user_name")->join("lara_users","lara_users.id","=","master_komponen.user_id");

        if (request()->has('search')) {
            $query->where(function($subquery){
                $subquery->where('master_komponen.name', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('master_komponen.type_name', 'LIKE', '%'.request()->input('search').'%');
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
            $query->orderBy(request('field'), request('direction'))->orderBy('master_komponen.created_at', request('direction'));
        } else {
            $query->orderBy("master_komponen.id", "desc");
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

    public function check_duplicate_name(Request $request)
    {
        $name = strtoupper($request->name);

        if(!empty($request->master_pihak_lain_id)){

            $master_pihak_lain = Sirkuler_master_pihak_lain::whereRaw("name LIKE '%$name%' AND id != $request->master_pihak_lain_id")->first();
        
        } else {

            $master_pihak_lain = Sirkuler_master_pihak_lain::whereRaw("name LIKE '%$name%'")->first();
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
        $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
        $master_gedungs = Gedung::whereRaw("is_active = 1")->get();
        $master_ruangans = Ruangan::whereRaw("is_active = 1")->get();
        $master_alats = Alat::whereRaw("is_active = 1 AND type = 2 OR type = 3")->get();
        $my_role = Sheet_role::whereRaw("user_id = $user_id AND is_active = 1")->get();

        foreach ($my_role as $key => $value) {
            $type_id[] = $value->type_id;
        }

        $master_types = Master_type::whereRaw("is_active = 1")
        ->whereIn("id", $type_id)->get();
        return Inertia::render('GASheet/MasterKomponen/Create', [
            'app_url' => env('APP_URL'),
            'master_lokasis' => $master_lokasis,
            'master_gedungs' => $master_gedungs,
            'master_ruangans' => $master_ruangans,
            'master_alats' => $master_alats,
            'master_types' => $master_types,
        ]);
    }

    public function get_ruangan(Request $request){
        $location = $request->value_location;
        $gedung = $request->value_gedung;

        $query_get_ruangan = Ruangan::whereRaw("location_id = ".$location['id'] ." AND gedung_id = ".$gedung['id']." AND type  =" .'2')->get();

        return response()->json([
            'success' => true,
            'master_ruangans'    => $query_get_ruangan,  
        ]);
    }

    public function get_default_simbol(Request $request){
        $alat = $request->alat;
        // dd($alat);
        $simbol_kondisis = Simbol_kondisi::whereRaw("alat_id = ".$alat['id'])
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
        // dd($request->alat_id);
        $user_id = auth()->user()->id;
        $master_komponen = new Komponen();
        $master_komponen->name = $request->name;
        $master_komponen->user_id = $user_id;
        $master_komponen->type = $request->type_id;
        $master_komponen->type_name = $request->type_name;
        $master_komponen->alat_id = $request->alat_id;
        $master_komponen->default_simbol_id = $request->default_simbol;
        if($request->is_active){

            $master_komponen->is_active = 1;
        } else {
            $master_komponen->is_active = 0;
        }

        $master_komponen->save();

        //redirect
        return redirect('/ga_sheet/komponen');
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
        $master_komponen = Komponen::find($id);

        return Inertia::render('GASheet/MasterKomponen/Edit', [
            'app_url' => env('APP_URL'),
            'master_komponen' => $master_komponen
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //

        $master_komponen = Komponen::find($id);
        $master_komponen->name = $request->name;

        if($request->is_active){

            $master_komponen->is_active = 1;
        } else {
            $master_komponen->is_active = 0;
        }

        $master_komponen->save();

        //redirect
        return redirect('/ga_sheet/komponen');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(master_komponen $master_komponen)
    // {
    //     $master_komponen->delete();

    //     $this->storeLogActivities("user delete data number of work", auth()->user()->id);
    //     return redirect()->route('master_komponen.index')
    //                     ->with('message', __('Number of work deleted successfully'));
    // }  
} 

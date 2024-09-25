<?php

namespace App\Http\Controllers\GASheet;

use App\Http\Controllers\Controller;

use App\Models\GASheet\Ruangan;
use App\Models\GASheet\Gedung;
use App\Models\GASheet\Lokasi;

use DB;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class MasterRuanganController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:master_ruangan list', ['only' => ['index', 'show']]);
        $this->middleware('can:master_ruangan create', ['only' => ['create', 'store']]);
        $this->middleware('can:master_ruangan edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:master_ruangan delete', ['only' => ['destroy']]);
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

        return Inertia::render('GASheet/MasterRuangan/Index', [
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

        $query = Ruangan::selectRaw("master_ruangan.*, lara_users.name AS user_name")->join("lara_users","lara_users.id","=","master_ruangan.user_id");

        if (request()->has('search')) {
            $query->where(function($subquery){
                $subquery->where('master_ruangan.user_id', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('master_ruangan.name', 'LIKE', '%'.request()->input('search').'%');
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
            $query->orderBy(request('field'), request('direction'))->orderBy('master_ruangan.created_at', request('direction'));
        } else {
            $query->orderBy("master_ruangan.id", "desc");
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
        $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
        $master_gedungs = Gedung::whereRaw("is_active = 1")->get();
        return Inertia::render('GASheet/MasterRuangan/Create', [
            'app_url' => env('APP_URL'),
            'master_gedungs' => $master_gedungs,
            'master_lokasis' => $master_lokasis,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $user_id = auth()->user()->id;
        $master_ruangan = new Ruangan();
        $master_ruangan->name = $request->name;
        $master_ruangan->user_id = $user_id;
        $master_ruangan->id_gedung = $request->value;
 
        if($request->is_active){

            $master_ruangan->is_active = 1;
        } else {
            $master_ruangan->is_active = 0;
        }

        $master_ruangan->save();

        //redirect
        return redirect('/ga_sheet/ruangan');
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
        $master_lokasis = Lokasi::whereRaw("is_active = 1")->get();
        $master_ruangan = Ruangan::find($id);
        $master_gedungs = Gedung::whereRaw("is_active = 1")->get();
        return Inertia::render('GASheet/MasterRuangan/Edit', [
            'app_url' => env('APP_URL'),
            'master_ruangan' => $master_ruangan,
            'master_gedungs' => $master_gedungs,
            'master_lokasis' => $master_lokasis,
        ]);
    }

    public function get_lokasi_gedung(Request $request){
        $ruangan = $request->ruangan;
        $gedung_id = $ruangan['id_gedung'];
        $gedung = Gedung::whereRaw("id = $gedung_id")->first();
        $result = [
            $gedung['id_lokasi'],
            $gedung_id,
            $ruangan['id'],
        ];
        return response()->json([
            'success' => true,
            'data_qr'    => $result 
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $user_update_id = auth()->user()->id;

        $master_ruangan = Ruangan::find($id);
        $master_ruangan->name = $request->name;
        $master_ruangan->id_gedung = $request->id_gedung;
        $master_ruangan->user_update_id = $user_update_id;

        if($request->is_active){

            $master_ruangan->is_active = 1;
        } else {
            $master_ruangan->is_active = 0;
        }

        $master_ruangan->save();

        //redirect
        return redirect('/ga_sheet/ruangan');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Master_ruangan $master_ruangan)
    // {
    //     $master_ruangan->delete();

    //     $this->storeLogActivities("user delete data number of work", auth()->user()->id);
    //     return redirect()->route('master_ruangan.index')
    //                     ->with('message', __('Number of work deleted successfully'));
    // }  
} 

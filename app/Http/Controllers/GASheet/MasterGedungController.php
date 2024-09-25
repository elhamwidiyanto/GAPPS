<?php

namespace App\Http\Controllers\GASheet;

use App\Http\Controllers\Controller;

use App\Models\GASheet\Gedung;
use App\Models\GASheet\Lokasi;

use DB;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class MasterGedungController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:master_gedung list', ['only' => ['index', 'show']]);
        $this->middleware('can:master_gedung create', ['only' => ['create', 'store']]);
        $this->middleware('can:master_gedung edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:master_gedung delete', ['only' => ['destroy']]);
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

        return Inertia::render('GASheet/MasterGedung/Index', [
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

        $query = Gedung::selectRaw("master_gedung.*, lara_users.name AS user_name")->join("lara_users","lara_users.id","=","master_gedung.user_id");

        if (request()->has('search')) {
            $query->where(function($subquery){
                $subquery->where('master_gedung.user_id', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('master_gedung.name', 'LIKE', '%'.request()->input('search').'%');
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
            $query->orderBy(request('field'), request('direction'))->orderBy('master_gedung.created_at', request('direction'));
        } else {
            $query->orderBy("master_gedung.id", "desc");
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
        return Inertia::render('GASheet/MasterGedung/Create', [
            'app_url' => env('APP_URL'),
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
        $master_gedung = new Gedung();
        $master_gedung->name = $request->name;
        $master_gedung->user_id = $user_id;
        $master_gedung->id_lokasi = $request->value;
        if($request->is_active){

            $master_gedung->is_active = 1;
        } else {
            $master_gedung->is_active = 0;
        }

        $master_gedung->save();

        //redirect
        return redirect('/ga_sheet/gedung');
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
        $master_gedung = Gedung::find($id);

        return Inertia::render('GASheet/MasterGedung/Edit', [
            'app_url' => env('APP_URL'),
            'master_gedung' => $master_gedung
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $user_update_id = auth()->user()->id;

        $master_gedung = Gedung::find($id);
        $master_gedung->name = $request->name;
        $master_gedung->user_update_id = $user_update_id;

        if($request->is_active){

            $master_gedung->is_active = 1;
        } else {
            $master_gedung->is_active = 0;
        }

        $master_gedung->save();

        //redirect
        return redirect('/ga_sheet/gedung');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Master_gedung $master_gedung)
    // {
    //     $master_gedung->delete();

    //     $this->storeLogActivities("user delete data number of work", auth()->user()->id);
    //     return redirect()->route('master_gedung.index')
    //                     ->with('message', __('Number of work deleted successfully'));
    // }  
} 

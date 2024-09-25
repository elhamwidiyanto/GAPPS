<?php

namespace App\Http\Controllers\GASheet;

use App\Http\Controllers\Controller;
use App\Models\GASheet\Master_type;
use App\Models\GASheet\Sheet_roles;

use DB;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class MasterRolesController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:sheet_roles list', ['only' => ['index', 'show']]);
        $this->middleware('can:sheet_roles create', ['only' => ['create', 'store']]);
        $this->middleware('can:sheet_roles edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:sheet_roles delete', ['only' => ['destroy']]);
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

        return Inertia::render('GASheet/SheetRoles/Index', [
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

        $query = Sheet_roles::selectRaw("sheet_roles.*, lara_users.name AS user_name")->join("lara_users","lara_users.id","=","sheet_roles.user_id");

        if (request()->has('search')) {
            $query->where(function($subquery){ 
                $subquery->where('sheet_roles.user_id', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('sheet_roles.type_id', 'LIKE', '%'.request()->input('search').'%');
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
            $query->orderBy(request('field'), request('direction'))->orderBy('sheet_roles.created_at', request('direction'));
        } else {
            $query->orderBy("sheet_roles.id", "desc");
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
        //
        $lara_users = User::whereRaw("is_active = 1")->get();
        $master_types = Master_type::whereRaw("is_active = 1")->get();

        return Inertia::render('GASheet/SheetRoles/Create', [
            'app_url' => env('APP_URL'),
            'lara_users' => $lara_users,
            'master_types' => $master_types
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $sheet_roles = new Sheet_roles();

        $sheet_roles->user_id = $request->user_id;
        $sheet_roles->name = $request->name;

        if(!empty($request->type_id['id'])){
            
            $sheet_roles->type_id = $request->type_id['id'];
        }
         
        if($request->is_active){
        
            $sheet_roles->is_active = 1;
        } else {
            $sheet_roles->is_active = 0;
        }

        $sheet_roles->save();

        return redirect('/ga_sheet/sheet_roles')->with('success', 'Sheet Roles berhasil ditambahkan!');
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
        $sheet_roles = Sheet_roles::find($id);

        $lara_users = User::whereRaw("is_active = 1")->get();
        // $lara_user = User::whereRaw("name = $sheet_roles->name")->first();
        $lara_user = User::whereRaw("name = ?", [$sheet_roles->name])->first();


        $master_types = Master_type::whereRaw("is_active = 1")->get(); 
        $master_type = Master_type::whereRaw("id = $sheet_roles->type_id")->first();


        return Inertia::render('GASheet/SheetRoles/Edit', [
            'app_url' => env('APP_URL'),
            'sheet_roles' => $sheet_roles,
            'master_types' => $master_types,
            'master_type' => $master_type,
            'lara_users' => $lara_users,
            'lara_user' => $lara_user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $sheet_roles = Sheet_roles::find($id);

        $sheet_roles->user_id = $request->user_id;
        $sheet_roles->name = $request->name;

        if(!empty($request->type_id['id'])){
            
            $sheet_roles->type_id = $request->type_id['id'];
        }
         
        if($request->is_active){
        
            $sheet_roles->is_active = 1;
        } else {
            $sheet_roles->is_active = 0;
        }

        $sheet_roles->save();

        return redirect('/ga_sheet/sheet_roles');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sheet_roles $sheet_roles)
    {
        $sheet_roles->delete();

        $this->storeLogActivities("user delete data number of work", auth()->user()->id);
        return redirect()->route('sheet_roles.index')
                        ->with('message', __('Number of work deleted successfully'));
    }  
} 

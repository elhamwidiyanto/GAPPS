<?php

namespace App\Http\Controllers\Sirkuler;

use App\Http\Controllers\Controller;

use App\Models\Sirkuler\Sirkuler_header;
use App\Models\Sirkuler\Sirkuler_header_history;
use App\Models\Sirkuler\Sirkuler_item_file;
use App\Models\Sirkuler\Sirkuler_item_file_history;
use App\Models\Sirkuler\Sirkuler_item_file_temp;
use App\Models\Sirkuler\Sirkuler_head_approval;
use App\Models\Sirkuler\Sirkuler_head_approval_history;
use App\Models\Employee\Param_company;
use App\Models\Sirkuler\Sirkuler_master_jenis_dokumen;
use App\Models\Sirkuler\Sirkuler_head_user;
use App\Models\Sirkuler\Sirkuler_master_pihak_lain;
use App\Models\Sirkuler\Sirkuler_master_type_pihak_lain;

use DB;

use App\Mail\Sirkuler\Sirkuler_approved_email;
use App\Mail\Sirkuler\Sirkuler_revised_email;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class MasterPihakLainController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:master_pihak_lain list', ['only' => ['index', 'show']]);
        $this->middleware('can:master_pihak_lain create', ['only' => ['create', 'store']]);
        $this->middleware('can:master_pihak_lain edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:master_pihak_lain delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user_id = auth()->user()->id;
        $hasRoles = auth()->user()->getRoleNames(); // Returns a collection

        $myRoles = [];
        foreach($hasRoles as $key => $value){
            $myRoles[] = $value;
        }

        $master_status = [
            [
                'id' => 10,
                'nama' => 'All'
            ],
            [
                'id' => 1,
                'nama' => 'Active'
            ],
            [
                'id' => 4,
                'nama' => 'Inactive'
            ],
        ];

        return Inertia::render('MasterPihakLain/Index', [
            'myRoles' => $myRoles,
            'app_url' => env('APP_URL'),
            'user_id' => $user_id,
            'master_status' => $master_status
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

        $query = Sirkuler_master_pihak_lain::selectRaw("*");

        if (request()->has('search')) {
            $query->where(function($subquery){
                $subquery->where('lara_sirkuler_master_pihak_lain.code', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('lara_sirkuler_master_pihak_lain.name', 'LIKE', '%'.request()->input('search').'%');
            });
        }

        if(request('search_status') == 4){

            $query->whereRaw('lara_sirkuler_master_pihak_lain.is_active = 0');

        } else {

            if(request('search_status')){
                if(request('search_status') != 10){
                    $query->whereRaw('lara_sirkuler_master_pihak_lain.is_active = '.request('search_status'));
                }
            }
        }

        if (request('field')) {
            $query->orderBy(request('field'), request('direction'))->orderBy('lara_sirkuler_master_pihak_lain.created_at', request('direction'));
        } else {
            $query->orderBy("lara_sirkuler_master_pihak_lain.id", "desc");
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
        //
        $master_type_pihak_lain = Sirkuler_master_type_pihak_lain::whereRaw("is_active = 1")->get();

        return Inertia::render('MasterPihakLain/Create', [
            'app_url' => env('APP_URL'),
            'master_type_pihak_lain' => $master_type_pihak_lain
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $new_master_pihak_lain = new Sirkuler_master_pihak_lain();
        $new_master_pihak_lain->code = strtoupper($request->code);
        $new_master_pihak_lain->name = strtoupper($request->name);

        if(!empty($request->company['id'])){
            $new_master_pihak_lain->company_id = $request->company['id'];
        } else {
            $new_master_pihak_lain->company_id = NULL;
        }

        $new_master_pihak_lain->master_type_pihak_lain_id = $request->master_type_pihak_lain['id'];
        $new_master_pihak_lain->master_type_pihak_lain_name = $request->master_type_pihak_lain['name'];
        $new_master_pihak_lain->master_type_pihak_lain_code = $request->master_type_pihak_lain['code'];

        if($request->master_type_pihak_lain['code'] != 'INTERNAL'){
            if($request->is_prefix){
                $new_master_pihak_lain->code = $request->master_type_pihak_lain['code'].'-'.strtoupper($request->code);
            }
        }
        
        if($request->is_active){
            $new_master_pihak_lain->is_active = 1;
        } else {
            $new_master_pihak_lain->is_active = 0;
        }

        $new_master_pihak_lain->save();

        //redirect
        return redirect('/sirkuler/master_pihak_lain');
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
        $user_id = auth()->user()->id;
        $master_pihak_lain = Sirkuler_master_pihak_lain::find($id);

        $company_choosed = [];

        if(!empty($master_pihak_lain->company_id)){
            $company_choosed = Param_company::whereRaw("id = $master_pihak_lain->company_id")->first();
        }

        $master_type_pihak_lain = Sirkuler_master_type_pihak_lain::whereRaw("is_active = 1")->get();

        $master_type_pihak_lain_choosed = Sirkuler_master_type_pihak_lain::whereRaw("id = $master_pihak_lain->master_type_pihak_lain_id")->first();

        return Inertia::render('MasterPihakLain/Edit', [
            'app_url' => env('APP_URL'),
            'master_pihak_lain' => $master_pihak_lain,
            'company_choosed' => $company_choosed,
            'master_type_pihak_lain' => $master_type_pihak_lain,
            'master_type_pihak_lain_choosed' => $master_type_pihak_lain_choosed,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $user_id = auth()->user()->id;

        $new_master_pihak_lain = Sirkuler_master_pihak_lain::find($id);

        $new_master_pihak_lain->code = strtoupper($request->code);
        $new_master_pihak_lain->name = strtoupper($request->name);

        if(!empty($request->company['id'])){

            $new_master_pihak_lain->company_id = $request->company['id'];

        } else {

            $new_master_pihak_lain->company_id = NULL;
        
        }

        $new_master_pihak_lain->master_type_pihak_lain_id = $request->master_type_pihak_lain['id'];
        $new_master_pihak_lain->master_type_pihak_lain_name = $request->master_type_pihak_lain['name'];
        $new_master_pihak_lain->master_type_pihak_lain_code = $request->master_type_pihak_lain['code'];

        if($request->master_type_pihak_lain['code'] != 'INTERNAL'){
            $new_master_pihak_lain->code = $request->master_type_pihak_lain['code'].'-'.strtoupper($request->code);
        }
        
        if($request->is_active){
            $new_master_pihak_lain->is_active = 1;
        } else {
            $new_master_pihak_lain->is_active = 0;
        }

        $new_master_pihak_lain->user_updated_id = $user_id;
        $new_master_pihak_lain->save();

        //redirect
        return redirect('/sirkuler/master_pihak_lain');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

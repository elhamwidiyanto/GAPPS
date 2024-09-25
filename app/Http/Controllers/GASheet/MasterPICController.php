<?php

namespace App\Http\Controllers\GASheet;

use App\Http\Controllers\Controller;

use App\Models\GASheet\Pic;


use DB;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class MasterPICController extends Controller
{
    public $timestamps = false;

    public function __construct()
    {
        $this->middleware('can:master_pic list', ['only' => ['index', 'show']]);
        $this->middleware('can:master_pic create', ['only' => ['create', 'store']]);
        $this->middleware('can:master_pic edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:master_pic delete', ['only' => ['destroy']]);
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

        return Inertia::render('GASheet/PiC/Index', [
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

        $query = Pic::selectRaw("pic.*, lara_users.name AS user_name")->join("lara_users","lara_users.id","=","pic.user_id");

        if (request()->has('search')) {
            $query->where(function($subquery){
                $subquery->where('pic.id', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('pic.pic_name', 'LIKE', '%'.request()->input('search').'%');
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
        return Inertia::render('GASheet/PiC/Create', [
            'app_url' => env('APP_URL'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $user_id = auth()->user()->id;
        $Pic = new pic();
        $Pic->pic_name = $request->name;
        $Pic->pic_phone = $request->phone;
        $Pic->pic_email = $request->email;
        $Pic->pic_nip = $request->nip;
        $Pic->user_id = $user_id;
        $Pic->company_id = $request->com_id;
        $Pic->company_name = $request->com_name;
        $Pic->departement_id = $request->dept_id;
        $Pic->departement_name = $request->dept_name;

        // if($request->is_active){

        //     $Pic->is_active = 1;
        // } else {
        //     $Pic->is_active = 0;
        // }

        $Pic->save();

        //redirect
        return redirect('/ga_sheet/pic');
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
        $Pic = pic::find($id);

        return Inertia::render('GASheet/PiC/Edit', [
            'app_url' => env('APP_URL'),
            'pic' => $Pic
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $user_id = auth()->user()->id;
        // dd($request->company_name);


        $Pic = pic::find($id);
        $Pic->pic_name = $request->pic_name;
        $Pic->pic_phone = $request->pic_phone;
        $Pic->pic_email = $request->pic_email;
        $Pic->pic_nip = $request->pic_nip;
        $Pic->user_id = $user_id;
        $Pic->company_name = $request->company_name;
        $Pic->departement_name = $request->departement_name;
        // if($request->is_active){

        //     $Pic->is_active = 1;
        // } else {
        //     $Pic->is_active = 0;
        // }

        $Pic->save();
        

        //redirect
        // return redirect('/ga_sheet/pic');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(pic $pic)
    {
        $pic->delete();

        $this->storeLogActivities("user delete data pic", auth()->user()->id);
        return redirect('/ga_sheet/pic')->with('message', __('Person in Charge deleted successfully'));
                        
}  
    
}

<?php

namespace App\Http\Controllers\GASheet;

use App\Http\Controllers\Controller;

use App\Models\GASheet\Category_preventive_breakdown;

use DB;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class MasterCategoryPreventiveBreakdownController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:master_category_preventive_breakdown list', ['only' => ['index', 'show']]);
        $this->middleware('can:master_category_preventive_breakdown create', ['only' => ['create', 'store']]);
        $this->middleware('can:master_category_preventive_breakdown edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:master_category_preventive_breakdown delete', ['only' => ['destroy']]);
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

        return Inertia::render('GASheet/CategoryPreventiveBreakdown/Index', [
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

        $query = Category_preventive_breakdown::selectRaw("category_preventive_breakdown.*, lara_users.name AS user_name")->join("lara_users","lara_users.id","=","category_preventive_breakdown.user_id");

        if (request()->has('search')) {
            $query->where(function($subquery){
                $subquery->where('category_preventive_breakdown.user_id', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('category_preventive_breakdown.name', 'LIKE', '%'.request()->input('search').'%');
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
            $query->orderBy(request('field'), request('direction'))->orderBy('category_preventive_breakdown.created_at', request('direction'));
        } else {
            $query->orderBy("category_preventive_breakdown.id", "desc");
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
        return Inertia::render('GASheet/CategoryPreventiveBreakdown/Create', [
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
        $category_preventive_breakdown = new Category_preventive_breakdown();
        $category_preventive_breakdown->name = $request->name;
        $category_preventive_breakdown->user_id = $user_id;

        if($request->is_active){

            $category_preventive_breakdown->is_active = 1;
        } else {
            $category_preventive_breakdown->is_active = 0;
        }

        $category_preventive_breakdown->save();

        //redirect
        return redirect('/ga_sheet/category_preventive_breakdown');
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
        $category_preventive_breakdown = Category_preventive_breakdown::find($id);

        return Inertia::render('GASheet/CategoryPreventiveBreakdown/Edit', [
            'app_url' => env('APP_URL'),
            'category_preventive_breakdown' => $category_preventive_breakdown
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $user_id = auth()->user()->id;

        $category_preventive_breakdown = Category_preventive_breakdown::find($id);
        $category_preventive_breakdown->name = $request->name;
        $category_preventive_breakdown->user_id = $user_id;

        if($request->is_active){

            $category_preventive_breakdown->is_active = 1;
        } else {
            $category_preventive_breakdown->is_active = 0;
        }

        $category_preventive_breakdown->save();

        //redirect
        return redirect('/ga_sheet/category_preventive_breakdown');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
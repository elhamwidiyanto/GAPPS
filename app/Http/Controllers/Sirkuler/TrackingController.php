<?php

namespace App\Http\Controllers\Sirkuler;

use App\Http\Controllers\Controller;
use App\Mail\Sirkuler\Sirkuler_approved_email;
use App\Models\Sirkuler\Sirkuler_header;
use App\Models\Sirkuler\Sirkuler_header_history;
use App\Models\Sirkuler\Sirkuler_item_file;
use App\Models\Sirkuler\Sirkuler_item_file_history;
use App\Models\Sirkuler\Sirkuler_item_file_temp;
use App\Models\Sirkuler\Sirkuler_head_approval;
use App\Models\Sirkuler\Sirkuler_head_approval_history;

use PDF;
use DB;

use App\Mail\Sirkuler\Sirkuler_posting_email;
use App\Mail\Sirkuler\Sirkuler_revised_email;
use App\Mail\Sirkuler\Sirkuler_finish_email;
use App\Mail\Sirkuler\Sirkuler_cancel_email;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class TrackingController extends Controller
{

    public function __construct()
    {
        // $this->middleware('can:sirkuler_create list', ['only' => ['index', 'show']]);
        // $this->middleware('can:sirkuler_create create', ['only' => ['create', 'store']]);
        // $this->middleware('can:sirkuler_create edit', ['only' => ['edit', 'update']]);
        // $this->middleware('can:sirkuler_create delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display a tracking list.
     */
    public function tracking(string $id)
    {
        //
        $sirkuler_header = Sirkuler_header::whereRaw("ticket_request_id = $id")->get();

        return Inertia::render('Tracking/Index', [
            'sirkuler_header' => $sirkuler_header,
            'ticket_request_id' => $id,
        ]);
    }

    public function time_tracking()
    {
        return Inertia::render('Tracking/TimeTracking', [
            
        ]);
    }

    public function get_catatan_history(string $id)
    {
        $sirkuler_header_history = Sirkuler_header_history::whereRaw("sirkuler_header_id = $id")->orderBy("id","DESC")->get();

        $sirkuler_header = Sirkuler_header::whereRaw("id = $id")->first();

        return response()->json([
            'success' => true,
            'data'    => $sirkuler_header_history,
            'data_header'    => $sirkuler_header,
        ]);
    }

    public function get_detail_sirkuler()
    {
        $sirkuler_id = request('sirkuler_id');

        $query = Sirkuler_head_approval::whereRaw("sirkuler_header_id = $sirkuler_id");

        if (request()->has('search')) {
            $query->where(function($subquery){
                $subquery->where('lara_sirkuler_head_approval.name', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('lara_sirkuler_head_approval.email', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('lara_sirkuler_head_approval.phone', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('lara_sirkuler_head_approval.position', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('lara_sirkuler_head_approval.department_name', 'LIKE', '%'.request()->input('search').'%')
                ->orWhere('lara_sirkuler_head_approval.company_name', 'LIKE', '%'.request()->input('search').'%');
            });
        }

        if (request('field')) {
            $query->orderBy(request('field'), request('direction'))->orderBy('lara_sirkuler_head_approval.created_at', request('direction'));
        } else {
            $query->orderBy("lara_sirkuler_head_approval.id", "asc");
        }

        $data = $query->paginate(5)->onEachSide(2);

        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

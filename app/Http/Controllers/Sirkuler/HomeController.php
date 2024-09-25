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
use App\Models\GASheet\Cleaning_sheet;
use App\Models\GASheet\Maintenance_sheet;
use App\Models\GASheet\IT_sheet;

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

class HomeController extends Controller
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
        $cleaning_diajukan_total = Cleaning_sheet::where('status', 1)->count();
        $cleaning_disetujui_total = Cleaning_sheet::where('status', 2)->count();
        $cleaning_ditolak_total = Cleaning_sheet::where('status', 3)->count();
        $cleaning_dicancel_total = Cleaning_sheet::where('status', 4)->count();
        $maintenance_diajukan_total = Maintenance_sheet::where('status', 1)->count();
        $maintenance_disetujui_total = Maintenance_sheet::where('status', 2)->count();
        $maintenance_ditolak_total = Maintenance_sheet::where('status', 3)->count();
        $maintenance_dicancel_total = Maintenance_sheet::where('status', 4)->count();
        $it_diajukan_total = it_sheet::where('status', 1)->count();
        $it_disetujui_total = it_sheet::where('status', 2)->count();
        $it_ditolak_total = it_sheet::where('status', 3)->count();
        $it_dicancel_total = it_sheet::where('status', 4)->count();
        // dd($cleaning_diajukan_total);
        return Inertia::render('Home/Index', [
           'cleaning_disetujui' => $cleaning_disetujui_total,
           'cleaning_diajukan' => $cleaning_diajukan_total,
           'cleaning_ditolak' => $cleaning_ditolak_total,
           'cleaning_dicancel' => $cleaning_dicancel_total,
           'maintenance_disetujui' => $maintenance_disetujui_total,
           'maintenance_diajukan' => $maintenance_diajukan_total,
           'maintenance_ditolak' => $maintenance_ditolak_total,
           'maintenance_dicancel' => $maintenance_dicancel_total,
           'it_disetujui' => $it_disetujui_total,
           'it_diajukan' => $it_diajukan_total,
           'it_ditolak' => $it_ditolak_total,
           'it_dicancel' => $it_dicancel_total,
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

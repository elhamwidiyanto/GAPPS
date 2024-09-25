<?php

namespace App\Http\Controllers\GASheet;

use App\Http\Controllers\Controller;

use App\Models\GASheet\Master_type;
use App\Models\GASheet\Simbol_kondisi;
use App\Models\GASheet\Alat;
use App\Models\GASheet\Calendar;
use App\Models\GASheet\Lokasi;
use App\Models\GASheet\Gedung;
use App\Models\GASheet\IT;
use App\Models\GASheet\IT_sheet;
use App\Models\GASheet\Ruangan;
use App\Models\GASheet\Sheet_role;
use App\Models\GASheet\Maintenance_sheet;
use App\Models\GASheet\Maintenance;
use App\Models\GASheet\Pic;
use DB;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Collection;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ReportGASheetController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:report list', ['only' => ['index', 'show']]);
        $this->middleware('can:report create', ['only' => ['create', 'store']]);
        $this->middleware('can:report edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:report delete', ['only' => ['destroy']]);
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
        $my_role = Sheet_role::whereRaw("user_id = $user_id AND is_active = 1")->get();

        foreach ($my_role as $key => $value) {
            $type_id[] = $value->type_id;
        }

        $master_types = Master_type::whereRaw("is_active = 1")
        ->whereIn("id", $type_id)->get();

        // $pics = Pic::whereRaw("type = 1")->get();

        return Inertia::render('GASheet/Report/Index', [
            'app_url' => env('APP_URL'),
            'user_id' => $user_id,
            'master_types' => $master_types,
            // 'pics' => $pics,
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

    public function get_alat(Request $request){
        $sheet = $request->sheet['id'];
        $alats = Alat::where('type', $sheet)
            ->get(['id', 'name', 'serial_number']);
        return response()->json([
            'alats' => $alats,
        ]);
    }

    public function get_pic(Request $request){
        // dd('Method hit!');
    
        $sheet = $request->sheet;
        $pics = Pic::where('type', $sheet)
            ->get(['id', 'pic_name']);
        return response()->json([
            'pics' => $pics,
        ]);
    }

    public function reportPdf(string $type, string $date, string $idReport, string $pic_name)
    {
        $result = '';
        $unique_komponen_name = '';
        $makerName = '';
        $asset_name = '';
        // dd($pic_name);
        $date = date("Y-m-d",strtotime($date));
        $year = date("Y",strtotime($date));
        $month = date("m",strtotime($date));

        if($type == 2){
            $makerName = auth()->user()->name;
            $maintenanceHeader_report = Maintenance_sheet::whereRaw("YEAR(date) = '$year' AND MONTH(date) = '$month'")->first();
            if(empty($maintenanceHeader_report)){
                throw ValidationException::withMessages(['error' => 'Data tidak ditemukan !']);
            }  

            $asset_name = Maintenance_sheet::find($idReport, 'alat_id')->first('alat_name');

            $maintenanceHeaders = Maintenance_sheet::where('alat_id', $idReport)
                ->where('status', 2)
                ->whereYear('created_at', $maintenanceHeader_report->created_at->year)
                ->whereMonth('created_at', $maintenanceHeader_report->created_at->month)
                ->get('id');
            $ids = $maintenanceHeaders->pluck('id')->toArray();
            $maintenanceTxRecords = Maintenance::whereIn('maintenance_header_id', $ids)
            ->get(['created_at', 'is_checked', 'komponen_name']);
        
            $komponenNames = $maintenanceTxRecords->pluck('komponen_name');

            $unique_komponen_name = $komponenNames->unique()->values()->toArray();

            $filteredRecords = $maintenanceTxRecords->filter(function ($record) {
                return $record->is_checked;
            });
            
            $groupedByComponent = $filteredRecords->groupBy('komponen_name');
            
            $currentDay = Carbon::now()->day;
            
            $result = [];
            foreach ($groupedByComponent as $component => $records) {
                $values = array_fill(0, $currentDay, 'x');

                foreach ($records as $record) {
                    $day = Carbon::parse($record->created_at)->day;
                    if ($day <= $currentDay) {
                        $values[$day - 1] = 'v'; 
                    }
                }
                $result[] = $values;
            }
        }
        else if($type == 3){
            $makerName = auth()->user()->name;
            $itHeader_report = IT_sheet::whereRaw("YEAR(date) = '$year' AND MONTH(date) = '$month'")->first();

            if(empty($itHeader_report)){
                throw ValidationException::withMessages(['error' => 'Data tidak ditemukan !']);
            }
 
            $asset_name = IT_sheet::where('alat_id', $idReport)->first('alat_name');
            
            $itHeaders = IT_sheet::where('alat_id', $idReport)
                ->where('status', 2)
                ->whereYear('created_at', $itHeader_report->created_at->year)
                ->whereMonth('created_at', $itHeader_report->created_at->month)
                ->get('id');
            $ids = $itHeaders->pluck('id')->toArray();
            $itTxRecords = IT::whereIn('it_header_id', $ids)
            ->get(['created_at', 'is_checked', 'komponen_name']);
        
            $komponenNames = $itTxRecords->pluck('komponen_name');

            $unique_komponen_name = $komponenNames->unique()->values()->toArray();

            $filteredRecords = $itTxRecords->filter(function ($record) {
                return $record->is_checked;
            });
            
            $groupedByComponent = $filteredRecords->groupBy('komponen_name');
            
            $currentDay = Carbon::now()->day;
            
            $result = [];
            foreach ($groupedByComponent as $component => $records) {
                $values = array_fill(0, $currentDay, 'x');

                foreach ($records as $record) {
                    $day = Carbon::parse($record->created_at)->day;
                    if ($day <= $currentDay) {
                        $values[$day - 1] = 'v'; 
                    }
                }
                $result[] = $values;
            }
        }
        else if($type == 1){
            $makerName = auth()->user()->name;
            $itHeader_report = IT_sheet::whereRaw("YEAR(date) = '$year' AND MONTH(date) = '$month'")->first();

            if(empty($itHeader_report)){
                throw ValidationException::withMessages(['error' => 'Data tidak ditemukan !']);
            }
 
            $asset_name = IT_sheet::where('alat_id', $idReport)->first('alat_name');
            
            $itHeaders = IT_sheet::where('alat_id', $idReport)
                ->where('status', 2)
                ->whereYear('created_at', $itHeader_report->created_at->year)
                ->whereMonth('created_at', $itHeader_report->created_at->month)
                ->get('id');
            $ids = $itHeaders->pluck('id')->toArray();
            $itTxRecords = IT::whereIn('it_header_id', $ids)
            ->get(['created_at', 'is_checked', 'komponen_name']);
        
            $komponenNames = $itTxRecords->pluck('komponen_name');

            $unique_komponen_name = $komponenNames->unique()->values()->toArray();

            $filteredRecords = $itTxRecords->filter(function ($record) {
                return $record->is_checked;
            });
            
            $groupedByComponent = $filteredRecords->groupBy('komponen_name');
            
            $currentDay = Carbon::now()->day;
            
            $result = [];
            foreach ($groupedByComponent as $component => $records) {
                $values = array_fill(0, $currentDay, 'x');

                foreach ($records as $record) {
                    $day = Carbon::parse($record->created_at)->day;
                    if ($day <= $currentDay) {
                        $values[$day - 1] = 'v'; 
                    }
                }
                $result[] = $values;
            }
        }
    
        $today = Carbon::today()->format('Y-m-d');
        $pdf = PDF::loadView("report.sheet_report", [
            "result" => $result,
            "daysInMonth" => 31,
            "komponen" => $unique_komponen_name,
            "maker" => $makerName,
            "alat_name" => $asset_name->alat_name,
            "today" => $today,
            "type" => $type,
            "date" => $date,
            "pic" => $pic_name,
        ])->setPaper("A4","landscape");

        return $pdf->stream("report-".date("m-Y").".pdf");
        // return $pdf->download("report-".date("m-Y").".pdf");
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
    public function destroy($id)
    {
        //
    }
}

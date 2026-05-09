<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee');

        // Filters
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        } else {
            $query->whereDate('date', '>=', Carbon::now()->startOfMonth());
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('department')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        $attendances = $query->orderBy('date', 'desc')->orderBy('time_in', 'desc')->get();
        
        $employees = Employee::orderBy('name')->get();
        $departments = Employee::whereNotNull('department')->distinct()->pluck('department');

        if ($request->has('export') && $request->export == 'csv') {
            return $this->exportCsv($attendances);
        }

        return view('attendance.history', compact('attendances', 'employees', 'departments'));
    }

    private function exportCsv($attendances)
    {
        $fileName = 'laporan_absensi_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Nama', 'Kode Karyawan', 'Departemen', 'Tanggal', 'Jam Masuk', 'Jam Keluar', 'Status', 'Match Distance'];

        $callback = function() use($attendances, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($attendances as $att) {
                $row['Nama']  = $att->employee->name;
                $row['Kode Karyawan'] = $att->employee->employee_code;
                $row['Departemen'] = $att->employee->department;
                $row['Tanggal'] = $att->date->format('Y-m-d');
                $row['Jam Masuk'] = $att->time_in;
                $row['Jam Keluar'] = $att->time_out;
                $row['Status'] = $att->status;
                $row['Match Distance'] = $att->match_distance;

                fputcsv($file, array($row['Nama'], $row['Kode Karyawan'], $row['Departemen'], $row['Tanggal'], $row['Jam Masuk'], $row['Jam Keluar'], $row['Status'], $row['Match Distance']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

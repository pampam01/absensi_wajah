<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function create()
    {
        return view('attendance.create');
    }

    public function employees()
    {
        // Return active employees with face descriptor
        $employees = Employee::whereNotNull('face_descriptor')
            ->where('is_active', true)
            ->select('id', 'name', 'face_descriptor')
            ->get();
            
        return response()->json($employees);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'match_distance' => 'required|numeric'
        ]);

        $employeeId = $request->employee_id;
        $today = Carbon::today();
        $now = Carbon::now()->format('H:i:s');

        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            // Check-in
            Attendance::create([
                'employee_id' => $employeeId,
                'date' => $today,
                'time_in' => $now,
                'match_distance' => $request->match_distance,
                'status' => 'present'
            ]);
            
            return response()->json(['message' => 'Absen masuk berhasil.']);
        } elseif (!$attendance->time_out) {
            // Check-out
            $attendance->update([
                'time_out' => $now
            ]);
            
            return response()->json(['message' => 'Absen keluar berhasil.']);
        } else {
            // Already complete
            return response()->json(['message' => 'Absensi hari ini sudah lengkap.'], 422);
        }
    }
}

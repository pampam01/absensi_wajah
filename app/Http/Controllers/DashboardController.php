<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        $totalEmployees = Employee::where('is_active', true)->count();
        $attendancesToday = Attendance::where('date', $today)->get();
        
        $totalPresent = $attendancesToday->count();
        $totalAbsent = $totalEmployees - $totalPresent;
        $totalCheckout = $attendancesToday->whereNotNull('time_out')->count();
        
        $recentAttendances = Attendance::with('employee')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalEmployees', 
            'totalPresent', 
            'totalAbsent', 
            'totalCheckout',
            'recentAttendances'
        ));
    }
}

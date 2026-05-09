<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FaceRegistrationController extends Controller
{
    public function create()
    {
        $employees = Employee::whereNull('face_descriptor')
            ->where('is_active', true)
            ->get();
            
        return view('face-registration.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'descriptor' => 'required|array',
            'photo' => 'required|string'
        ]);

        $employee = Employee::findOrFail($request->employee_id);

        if ($employee->face_descriptor) {
            return response()->json(['message' => 'Wajah karyawan ini sudah terdaftar.'], 422);
        }

        // Process base64 photo
        $photoData = $request->photo;
        $photoData = str_replace('data:image/jpeg;base64,', '', $photoData);
        $photoData = str_replace('data:image/png;base64,', '', $photoData);
        $photoData = str_replace(' ', '+', $photoData);
        $photoName = 'faces/' . Str::uuid() . '.jpg';
        
        Storage::disk('public')->put($photoName, base64_decode($photoData));

        $employee->update([
            'face_descriptor' => $request->descriptor,
            'photo_path' => $photoName,
            'registered_by' => auth()->id()
        ]);

        return response()->json(['message' => 'Wajah berhasil didaftarkan.']);
    }
}

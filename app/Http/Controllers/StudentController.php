<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    // Get all students for DataTable
    public function index()
    {
        $students = Student::all();
        return response()->json(['data' => $students]);
    }

    // Store new student
    public function store(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email|max:255',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle file upload
        $file = $request->file('avatar');
        $filename = time().'.'.$file->getClientOriginalExtension();
        $file->storeAs('public/images', $filename);

        $student = Student::create([
            'first_name' => $request->fname,
            'last_name' => $request->lname,
            'email' => $request->email,
            'avatar' => $filename,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Student created successfully',
            'data' => $student
        ]);
    }

    // Edit student (returns student data)
    public function edit($id)
    {
        $student = Student::findOrFail($id);
        return response()->json($student);
    }

    // Update student
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,'.$id.'|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'first_name' => $request->fname,
            'last_name' => $request->lname,
            'email' => $request->email,
        ];

        // Handle file upload if new avatar provided
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($student->avatar && file_exists(storage_path('app/public/images/'.$student->avatar))) {
                unlink(storage_path('app/public/images/'.$student->avatar));
            }

            $file = $request->file('avatar');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/images', $filename);
            $data['avatar'] = $filename;
        }

        $student->update($data);

        return response()->json([
            'status' => 200,
            'message' => 'Student updated successfully'
        ]);
    }

    // Delete student
    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        // Delete avatar file if exists
        if ($student->avatar && file_exists(storage_path('app/public/images/'.$student->avatar))) {
            unlink(storage_path('app/public/images/'.$student->avatar));
        }

        $student->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Student deleted successfully'
        ]);
    }
}

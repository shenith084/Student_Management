<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Get all students for DataTable
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $students = Student::all();
        return response()->json(['data' => $students]);
    }

    /**
     * Store new student
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email|max:255',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $avatarPath = $this->storeAvatar($request->file('avatar'));

            $student = Student::create([
                'first_name' => $validated['fname'],
                'last_name' => $validated['lname'],
                'email' => $validated['email'],
                'avatar' => $avatarPath,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Student created successfully',
                'data' => $student
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error creating student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student data for editing
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            $student = Student::findOrFail($id);
            return response()->json($student);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Student not found'
            ], 404);
        }
    }

    /**
     * Update student
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,'.$id.'|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $student = Student::findOrFail($id);
            $data = [
                'first_name' => $validated['fname'],
                'last_name' => $validated['lname'],
                'email' => $validated['email'],
            ];

            if ($request->hasFile('avatar')) {
                $this->deleteAvatar($student->avatar);
                $data['avatar'] = $this->storeAvatar($request->file('avatar'));
            }

            $student->update($data);

            return response()->json([
                'status' => 200,
                'message' => 'Student updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error updating student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete student
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $this->deleteAvatar($student->avatar);
            $student->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Student deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error deleting student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store avatar file
     * @param $file
     * @return string
     */
    private function storeAvatar($file)
    {
        $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->storeAs('public/images', $filename);
        return $filename;
    }

    /**
     * Delete avatar file
     * @param $filename
     */
    private function deleteAvatar($filename)
    {
        if ($filename && Storage::exists('public/images/'.$filename)) {
            Storage::delete('public/images/'.$filename);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group 2. Students
 *
 * APIs for managing student data
 */
class StudentApiController extends Controller
{
    /**
     * List Students by Class
     *
     * Get all active students for a specific class.
     *
     * @pathParam classId int required Class ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {"id": 1, "nisn": "1234567890", "name": "John Doe", "gender": "laki-laki"},
     *     ...
     *   ]
     * }
     */
    public function byClass(Request $request, int $classId): JsonResponse
    {
        $class = ClassModel::findOrFail($classId);

        $user = $request->user();
        if ($class->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'error' => 'Class not found',
            ], 404);
        }

        $students = $class->students()
            ->where('is_active', true)
            ->select(['id', 'nisn', 'nis', 'name', 'gender'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    /**
     * Get Student Details
     *
     * Get detailed information for a specific student.
     *
     * @pathParam studentId int required Student ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "nisn": "1234567890",
     *     "name": "John Doe",
     *     "gender": "laki-laki",
     *     "birth_date": "2008-01-15",
     *     "birth_place": "Jakarta",
     *     "father_name": "Robert Doe",
     *     "mother_name": "Jane Doe",
     *     "poin": 95,
     *     "class": {"id": 1, "name": "X IPA 1"}
     *   }
     * }
     */
    public function show(Request $request, int $studentId): JsonResponse
    {
        $student = Student::with('classModel')->findOrFail($studentId);

        $user = $request->user();
        if ($student->classModel->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'error' => 'Student not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $student->id,
                'nisn' => $student->nisn,
                'nis' => $student->nis,
                'name' => $student->name,
                'gender' => $student->gender,
                'birth_date' => $student->birth_date?->format('Y-m-d'),
                'birth_place' => $student->birth_place,
                'religion' => $student->religion,
                'address' => $student->address,
                'father_name' => $student->father_name,
                'mother_name' => $student->mother_name,
                'parent_phone' => $student->parent_phone,
                'poin' => $student->poin,
                'class' => [
                    'id' => $student->classModel->id,
                    'name' => $student->classModel->name,
                ],
            ],
        ]);
    }

    /**
     * Get Student Photo
     *
     * Get student photo for CBT/exam browser integration.
     *
     * @pathParam studentId int required Student ID. Example: 1
     *
     * @response 200 image/jpeg
     * @response 404 No photo available
     */
    public function photo(Request $request, int $studentId): \Illuminate\Http\Response
    {
        $student = Student::findOrFail($studentId);

        $user = $request->user();
        if ($student->classModel->user_id !== $user->id) {
            abort(404);
        }

        if ($student->photo && file_exists(storage_path('app/' . $student->photo))) {
            return response()->file(storage_path('app/' . $student->photo));
        }

        return response()->noContent(404);
    }
}

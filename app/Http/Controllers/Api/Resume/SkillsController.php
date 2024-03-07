<?php

namespace App\Http\Controllers\Api\Resume;

use App\Models\Profile;
use App\Models\Project;
use App\Models\projects;
use App\Models\Skill;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkillsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::where("id", auth()->user()->id)->firstOrFail();
        $profiles = profile::where("users_id", $user->id)->firstOrFail();
        $skill = Skill::where("profiles_id", $profiles->id)->get();
        $skillData = $skill->map(function ($skill) {
            return [
                'id' => $skill->id,
                'name' => $skill->name,
                'level' => $skill->level,
            ];
        });

        // Trả về danh sách giáo dục dưới dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $skillData
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Validator $validator)
    {
        $user = auth()->user();
        $profile = $user->profile->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'User does not have a profile',
            ], 400);
        }

        $profiles_id = $profile->id;

        $data = $request->all();

        $validationRules = [
//            'skills' => 'required|array'sss
            'skills.*.name' => 'required|string',
            'skills.*.level' => 'required|numeric',
        ];

        $validation = $validator::make($data, $validationRules);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validation->errors(),
            ], 400);
        }

        // Chuẩn bị dữ liệu cho việc tạo nhiều skill
        $skillsData = array_map(function ($skill) use ($profiles_id) {
            return [
                'name' => $skill['name'],
                'level' => $skill['level'],
                'profiles_id' => $profiles_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $data['skills']);

        // Tạo nhiều skill cùng một lúc
        Skill::insert($skillsData);

        return response()->json([
            'success' => true,
            'message' => "Skills created successfully",
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(Skill $skill)
    {
        $user = User::where("id", auth()->user()->id)->first();
        $profile = $user->profile->first();

        if ($skill->profiles_id == $profile->id) {
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $skill
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'fail'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Skill $skill)
    {
        $data = $request->all();

        $skill->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'data' => $skill,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill)
    {
        $skill->delete();
        return response()->json([
            'success' => true,
            'message' => 'Skill deleted successfully',
        ]);
    }
}

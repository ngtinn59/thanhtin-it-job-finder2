<?php

namespace App\Http\Controllers\Api\Resume;

use App\Models\Certificate;
use App\Models\Experience;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExperiencesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::where("id", auth()->user()->id)->firstOrFail();
        $profiles = profile::where("users_id", $user->id)->firstOrFail();
        $experience = Experience::where("profiles_id", $profiles->id)->get();
        $experienceData = $experience->map(function ($experience) {
            return [
                'position' => $experience->position,
                'company' => $experience->company,
                'start_date' => $experience->start_date,
                'end_date' => $experience->end_date,
                'responsibilities' => $experience->responsibilities,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $experienceData
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = User::where("id", auth()->user()->id)->first();
        $profile = $user->profile->first();
        $profiles = $profile->id;
        $data = [
            'position' => $request->input('position'),
            'company' => $request->input('company'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'responsibilities' => $request->input('responsibilities'),
            'profiles_id' => $profiles
        ];
        $validator = Validator::make($data, [
            'position' => 'required',
            'company' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'responsibilities' => 'required',
            'profiles_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }
       $data = $validator->validated();
        $experience = Experience::create($data);

        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $experience
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Experience $experience)
    {
        $user = User::where("id", auth()->user()->id)->first();
        $profile = $user->profile->first();
        if ($experience->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to the award',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => [
                'position' => $experience->position,
                'company' => $experience->company,
                'start_date' => $experience->start_date,
                'end_date' => $experience->end_date,
                'responsibilities' => $experience->responsibilities,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Experience $experience)
    {
        $data = $request->all();


        $experience->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Experience updated successfully',
            'data' => $experience,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Experience $experience)
    {

        $experience->delete();
        return response()->json([
            'success' => true,
            'message' => 'Experience deleted successfully',
            'data' => $experience,
        ]);
    }
}

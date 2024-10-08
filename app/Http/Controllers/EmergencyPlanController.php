<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EmergencyPlanController extends Controller
{
    /**
     * Zeigt alle Notfallpläne an.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $plans = Cache::get('emergency_plans', []);

        return response()->json([
            'data' => array_values($plans)
        ], 200);
    }

    /**
     * Speichert einen neuen Notfallplan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'details' => 'required|string',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'eligible_for_emergency' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed for storing emergency plan:', $validator->errors()->toArray());
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $plans = Cache::get('emergency_plans', []);
        $id = empty($plans) ? 1 : max(array_keys($plans)) + 1;

        $plan = [
            'id' => $id,
            'name' => $request->input('name'),
            'details' => $request->input('details'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'eligible_for_emergency' => $request->input('eligible_for_emergency'),
        ];

        $plans[$id] = $plan;
        Cache::forever('emergency_plans', $plans);

        Log::info('Emergency plan stored successfully:', $plan);

        return response()->json([
            'status' => 'success',
            'id' => $id
        ], 201);
    }

    /**
     * Aktualisiert einen bestehenden Notfallplan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $plans = Cache::get('emergency_plans', []);

        if (!isset($plans[$id])) {
            Log::warning("Attempted to update non-existing emergency plan with ID: $id");
            return response()->json([
                'status' => 'error',
                'message' => 'Emergency plan not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'details' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'eligible_for_emergency' => 'sometimes|required|boolean',
        ]);

        if ($validator->fails()) {
            Log::warning("Validation failed for updating emergency plan with ID: $id", $validator->errors()->toArray());
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Aktualisieren der Felder, wenn sie im Request vorhanden sind
        foreach ($request->only(['name', 'details', 'email', 'phone', 'eligible_for_emergency']) as $key => $value) {
            $plans[$id][$key] = $value;
        }

        Cache::forever('emergency_plans', $plans);

        Log::info("Emergency plan with ID: $id updated successfully:", $plans[$id]);

        return response()->json([
            'status' => 'success'
        ], 200);
    }

    /**
     * Löscht einen bestehenden Notfallplan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $plans = Cache::get('emergency_plans', []);

        if (!isset($plans[$id])) {
            Log::warning("Attempted to delete non-existing emergency plan with ID: $id");
            return response()->json([
                'status' => 'error',
                'message' => 'Emergency plan not found.'
            ], 404);
        }

        unset($plans[$id]);
        Cache::forever('emergency_plans', $plans);

        Log::info("Emergency plan with ID: $id deleted successfully.");

        return response()->json([
            'status' => 'success'
        ], 200);
    }

    /**
     * Löscht den Notfallpläne-Cache.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache()
    {
        Cache::forget('emergency_plans');

        Log::info("Emergency plans cache cleared.");

        return response()->json([
            'status' => 'success',
            'message' => 'Cache cleared'
        ], 200);
    }
}

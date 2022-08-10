<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResponsibilityRequest;
use App\Models\Responsibility;
use Exception;


class ResponsibilityController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $Responsibilty = Responsibility::query();


        // Get single data
        if ($id) {
            $responsibilty = $Responsibilty->find($id);

            if ($responsibilty) {
                return ResponseFormatter::success($responsibilty, 'Responsibility found');
            }

            return ResponseFormatter::error('Responsibility not found', 404);
        }

        // Get multiple data
        $responsibilty = $Responsibilty;

        if ($name) {
            $responsibilty->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $responsibilty->paginate($limit),
            'responsibilitys found'
        );
    }

    public function create(CreateResponsibilityRequest  $request)
    {
        try {

            // Create Responsibility
            $responsibilty = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->role_id
            ]);

            if (!$responsibilty) {
                throw new Exception('responsibilty not created');
            }


            return ResponseFormatter::success($responsibilty, 'Responsibility created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {

        try {
            // Get responsibiltys
            $responsibilty = Responsibility::find($id);

            // Check if responsibiltys exists
            if (!$responsibilty) {
                throw new Exception('Responsibility not found');
            }


            // Update responsibiltys
            $responsibilty->delete();

            return ResponseFormatter::success('Responsibility Deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}

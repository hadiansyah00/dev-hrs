<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Requests\CreateTeamRequest;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $teamsQuery = Team::query();


        // Get single data
        if ($id) {
            $teams = $teamsQuery->find($id);

            if ($teams) {
                return ResponseFormatter::success($teams, 'teams found');
            }

            return ResponseFormatter::error('Team not found', 404);
        }

        // Get multiple data
        $teams = $teamsQuery;

        if ($name) {
            $teams->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Companies found'
        );
    }

    public function create(CreateTeamRequest $request)
    {
        try {
            // Upload Icon
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // Create teams
            $team = Team::create([
                'name' => $request->name,
                'icon' => $path,
                'teams_id' => $request->teams_id
            ]);

            if (!$team) {
                throw new Exception('Team not created');
            }


            return ResponseFormatter::success($team, 'Team created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
    public function update(UpdateTeamRequest $request, $id)
    {

        try {
            // Get teams
            $team = Team::find($id);

            // Check if teams exists
            if (!$team) {
                throw new Exception('Team not found');
            }

            // Upload logo
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // Update teams
            $team->update([
                'name' => $request->name,
                'icon' => $path,
                'teams_id' => $request->teams_id
            ]);

            return ResponseFormatter::success($team, 'Team updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
    public function destroy($id)
    {

        try {
            // Get teams
            $team = Team::find($id);

            // Check if teams exists
            if (!$team) {
                throw new Exception('Team not found');
            }


            // Update teams
            $team->delete();

            return ResponseFormatter::success('Team Deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}

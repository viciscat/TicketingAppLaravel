<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function apiGetSuggestions(Request $request) {
        $query = User::query();
        $validated = $request->validate([
            "input" => ["nullable", "string"],
            "in_project_id" => ["nullable", "integer", "exists:projects,id"],
        ]);
        if (!empty($validated["input"])) {
            $input = $validated["input"];
            $value = "%" . $input . "%";
            $query->where(function ($q) use ($value) {
                $q->where("first_name", "like", $value)
                    ->orWhere("last_name", "like", $value)
                    ->orWhere("email", "like", $value);
            });
        }
        if (!empty($validated["in_project_id"])) $query->where(function ($q) use ($validated) {
            $q->whereRelation(
                "projects",
                "id",
                '=',
                $validated["in_project_id"])->orWhere('role', '=', UserRole::ADMIN);
        });
        if ($request->boolean("no-client")) $query->where('role', '!=', UserRole::CLIENT);

        $users = $query->orderBy("first_name")->take(10)->skip(0)->get();
        $users = $users->map(function ($user) {
            return [
                "full_name" => $user->fullName(),
                "email" => $user['email']
            ];
        })->values()->all();
        return response()->json($users);
    }
}

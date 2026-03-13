<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function list(){
        return view('projects.list');
    }
    public function create(){
        return view('projects.create');
    }
    public function view(){
        return view('projects.view');
    }
}

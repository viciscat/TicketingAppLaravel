<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function list(){
        return view('tickets.list');
    }
    public function create(){
        return view('tickets.create');
    }
    public function view(){
        return view('tickets.view');
    }
}

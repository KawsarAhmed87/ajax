<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\Location;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $roles = Role::all();
        $districts = Location::where('parent_id', 0)->orderBy('location_name', 'asc')->get();
        return view('home', compact('roles', 'districts'));
    }

    public function store(Request $request){
        dd($request->all());
    }

    public function upazila_list(Request $request){
        if ($request->ajax()) {
            if ($request->district_id) {
                $output = '<option value="">Select One</option>';
                $upazilas = Location::where('parent_id', $request->district_id)->orderBy('location_name', 'asc')->get();
                if (!$upazilas->isEmpty()) {
                    foreach($upazilas as $data){
                        $output .= '<option value="'.$data->id.'">'.$data->location_name.'</option>';
                    }
                }
                return response()->json($output);
            }

        }
    }
}

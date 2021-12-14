<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\Location;
use App\Http\Requests\UserFormRequest;
use App\User;
use App\Traits\Uploadable;

class HomeController extends Controller
{
    use Uploadable;
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

    public function userList(Request $request){
        if ($request->ajax()) {
             $user = new User();

            $user->setOrderValue($request->input('order.0.column'));
            $user->setDirValue($request->input('order.0.dir'));
            $user->setLengthValue($request->input('length'));
            $user->setStartValue($request->input('start'));
        }
    }

    public function store(UserFormRequest $request){
       $data = $request->validated();
      // $result = User::updateOrCreate(['id' => $request->update_id],$data);

       $collection = collect($data)->except(['avatar', 'password_confirmation']);
        if ($request->file('avatar')) {
            $avatar = $this->upload_file($request->file('avatar'), USER_AVATAR);
            $collection = $collection->merge(compact('avatar'));

            /*if (!empty($request->old_avatar)) {
                $this->delete_file($request->old_avatar, USER_AVATAR);
            }*/
            
        }
        $result = User::updateOrCreate(['id' => $request->update_id], $collection->all());

       if ($request) {
           $output = ['status' => 'success', 'message' => 'Data inserted'];
       }else{
        $output = ['status' => 'error', 'message' => 'Data not inserted'];
       }
       return response()->json($output);
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

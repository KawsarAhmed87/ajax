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

             if (!empty($request->name)) {
                $user->setName($request->name);
            }
            if (!empty($request->email)) {
                $user->setEmail($request->email);
            }
            if (!empty($request->mobile_no)) {
                $user->setMobileNo($request->mobile_no);
            }
            if (!empty($request->role_id)) {
                $user->setRoleID($request->role_id);
            }
            if (!empty($request->district_id)) {
                $user->setDistrictID($request->district_id);
            }
            if (!empty($request->upazila_id)) {
                $user->setUpazilaID($request->upazila_id);
            }
            if (!empty($request->status)) {
                $user->setStatus($request->status);
            }

            $user->setOrderValue($request->input('order.0.column'));
            $user->setDirValue($request->input('order.0.dir'));
            $user->setLengthValue($request->input('length'));
            $user->setStartValue($request->input('start'));

            $list = $user->getList();

            $data = [];
            $no = $request->input('start');
            foreach ($list as $value) {
                $no++;
                $action = '';
                $action .= ' <a class="dropdown-item edit_data" data-id="' . $value->id . '"><i class="fas fa-edit text-primary"></i> Edit</a>';
                $action .= ' <a class="dropdown-item view_data"  data-id="' . $value->id . '"><i class="fas fa-eye text-warning"></i> View</a>';
                $action .= ' <a class="dropdown-item delete_data"  data-id="' . $value->id . '" data-name="' . $value->name . '"><i class="fas fa-trash text-danger"></i> Delete</a>';

                $btngroup = '<div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-th-list"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                ' . $action . '
                </div>
              </div>';

                $row = [];
               

              $row[] = '<div class="custom-control custom-checkbox">
                <input type="checkbox" value="'.$value->id.'"
                class="custom-control-input select_data" onchange="select_single_item('.$value->id.')" id="checkbox'.$value->id.'">
                <label class="custom-control-label" for="checkbox'.$value->id.'"></label>
              </div>';

              
                $row[] = $no;
                $row[] = $this->avatar($value->avatar, $value->name);
                $row[] = $value->name;
                $row[] = $value->role->role_name;
                $row[] = $value->email;
                $row[] = $value->mobile_no;
                $row[] = $value->district->location_name;
                $row[] = $value->upazila->location_name;
                $row[] = $value->postal_code;
                $row[] = $value->email_verified_at ? '<span class="badge badge-pill badge-success">Verified</span>' : '<span class="badge badge-pill badge-danger">Unverified</span>';
                $row[] = $this->toggle_button($value->status,$value->id);
                $row[] = $btngroup;
                $data[] = $row;
            }
            $output = array(
                "draw" => $request->input('draw'),
                "recordsTotal" => $user->count_all(),
                "recordsFiltered" => $user->count_filtered(),
                "data" => $data,
            );

            echo json_encode($output);
        }
    }

     private function avatar($avatar = null, $name)
    {
        return !empty($avatar) ? '<img src="' . asset("storage/" . USER_AVATAR . $avatar) . '" alt="' . $name . '" style="width:60px;"/>' : '<img style="width:60px;" src="' . asset("svg/user.svg") . '" alt="User Avatar"/>';
    }

      private function toggle_button($status,$id){
            $checked = $status == 1 ? 'checked' : '';
            return    '<label class="switch">
                        <input type="checkbox" '.$checked.' class="change_status" data-id="'.$id.'">
                        <span class="slider round"></span>
                        </label>';
    }

    public function store(UserFormRequest $request){
       $data = $request->validated();
      // $result = User::updateOrCreate(['id' => $request->update_id],$data);

       $collection = collect($data)->except(['avatar', 'password_confirmation']);
        if ($request->file('avatar')) {
            $avatar = $this->upload_file($request->file('avatar'), USER_AVATAR);
            $collection = $collection->merge(compact('avatar'));

            if (!empty($request->old_avatar)) {
                $this->delete_file($request->old_avatar, USER_AVATAR);
            }
            
        }


        $result = User::updateOrCreate(['id' => $request->update_id], $collection->all());

       if ($request) {
        
           $output = ['status' => 'success', 'message' => 'Data inserted'];
       }else{

         if (!empty($avatar)) {
                $this->delete_file($avatar, USER_AVATAR);
            }
            
        $output = ['status' => 'error', 'message' => 'Data not inserted'];
       }
       return response()->json($output);
    }

     public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with(['role:id,role_name', 'district:id,location_name',
                'upazila:id,location_name'])->find($request->id);
            if ($data) {
                $output['user_view'] = view('user_details', compact('data'))->render();
                $output['name'] = $data->name;
            } else {
                $output['user_view'] = '';
                $output['name'] = '';
            }
            return response()->json($output);
        }
    }

    public function edit(Request $request){
        if ($request->ajax()) {
            $data = User::toBase()->find($request->id);
            if ($data) {
                $output['user'] = $data;
            }else{
                $output['user'] = '';
            }
            return response()->json($output);
        }
    }

    public function destroy(Request $request){
        if ($request->ajax()) {
            $data = User::find($request->id);
            if ($data) {
                $avatar = $data->avatar;
                if ($data->delete()) {
                    if (!empty($avatar)) {
                        $this->delete_file($avatar, USER_AVATAR);
                    }
                    $output = ['status' => 'success', 'message' => 'Data deleted successfully'];
                } else {
                    $output = ['status' => 'error', 'message' => 'Data cannot delete!'];
                }
            } else {
                $output = ['status' => 'error', 'message' => 'Data cannot delete!'];
            }
            return response()->json($output);
        }
    }

    public function bulkActionDelete(Request $request)
    {
        if ($request->ajax()) {
            $avatars = User::toBase()->select('avatar')->whereIn('id',$request->id)->get();
            $result = User::destroy($request->id);
            if ($result) {
                if(!empty($avatars)){
                    foreach ($avatars as $value) {
                        if (!empty($value->avatar)) {
                            $this->delete_file($value->avatar, USER_AVATAR);
                        }
                    }
                }
                $output = ['status' => 'success', 'message' => 'Data has been deleted successfully'];
            } else {
                $output = ['status' => 'error', 'message' => 'Data cannot delete!'];
            }
            return response()->json($output);
        }
    }


     public function changeStatus(Request $request)
    {
        if ($request->ajax()) {
            if ($request->id && $request->status) {
                $result = User::find($request->id)->update(['status'=>$request->status]);
                if ($result) {
                    $output = ['status' => 'success', 'message' => 'User status changed successfully'];
                } else {
                    $output = ['status' => 'error', 'message' => 'User status cannot change'];
                }
            } else {
                $output = ['status' => 'error', 'message' => 'User status cannot change'];
            }
            return response()->json($output);
        }
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

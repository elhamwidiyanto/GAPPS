<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use App\Models\Master\Production\Master_formation;
// use App\Models\Master\Production\Master_shift;
use App\Models\Role;
use App\Models\User;
use BalajiDharma\LaravelAdminCore\Actions\User\CreateUser;
use BalajiDharma\LaravelAdminCore\Actions\User\UpdateUser;
use BalajiDharma\LaravelAdminCore\Requests\StoreUserRequest;
use BalajiDharma\LaravelAdminCore\Requests\UpdateUserRequest;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:user list', ['only' => ['index', 'show']]);
        $this->middleware('can:user create', ['only' => ['create', 'store']]);
        $this->middleware('can:user edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:user delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $users = (new User)->newQuery();

        if (request()->has('search')) {
            $users->where('name', 'Like', '%'.request()->input('search').'%');
        }

        if (request()->query('sort')) {
            $attribute = request()->query('sort');
            $sort_order = 'ASC';
            if (strncmp($attribute, '-', 1) === 0) {
                $sort_order = 'DESC';
                $attribute = substr($attribute, 1);
            }
            $users->orderBy($attribute, $sort_order);
        } else {
            $users->latest();
        }

        $users = $users->paginate(5)->onEachSide(2)->appends(request()->query());

        return Inertia::render('Admin/User/Index', [
            'users' => $users,
            'filters' => request()->all('search'),
            'can' => [
                'create' => Auth::user()->can('user create'),
                'edit' => Auth::user()->can('user edit'),
                'delete' => Auth::user()->can('user delete'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $roles = Role::all()->pluck("name","id");

        // $shifts = Master_shift::All();
        // $formations = Master_formation::All();

        return Inertia::render('Admin/User/Create', [
            'roles' => $roles,
            // 'shifts' => $shifts,
            // 'formations' => $formations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreUserRequest  $request
     * @param  CreateUser  $createUser
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request, CreateUser $createUser)
    {
        $createUser->handle((object) $request->all());

        $this->storeLogActivities("user store data user account", auth()->user()->id);
        return redirect()->route('user.index')
                        ->with('message', __('User created successfully.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Inertia\Response
     */
    public function show(User $user)
    {
        $roles = Role::all()->pluck("name","id");
        $userHasRoles = array_column(json_decode($user->roles, true), 'id');

        return Inertia::render('Admin/User/Show', [
            'user' => $user,
            'roles' => $roles,
            'userHasRoles' => $userHasRoles,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Inertia\Response
     */
    public function edit(User $user)
    {
        $roles = Role::all()->pluck("name","id");
        $userHasRoles = array_column(json_decode($user->roles, true), 'id');

        // $shifts = Master_shift::All();
        // $formations = Master_formation::All();

        // $shift = Master_shift::where("id","=",$user->lara_master_shift_id)->first();
        // $formation = Master_formation::where("id","=",$user->lara_master_formation_id)->first();

        return Inertia::render('Admin/User/Edit', [
            'user' => $user,
            'roles' => $roles,
            'userHasRoles' => $userHasRoles,
            // 'shifts' => $shifts,
            // 'shift' => $shift,
            // 'formations' => $formations,
            // 'formation' => $formation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @param  UpdateUser  $updateUser
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUser $updateUser)
    {
        $updateUser->handle((object) $request->all(), $user);

        $this->storeLogActivities("user update data user account", auth()->user()->id);
        return redirect()->route('user.index')
                        ->with('message', __('User updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $user->delete();

        $this->storeLogActivities("user delete data user account", auth()->user()->id);
        return redirect()->route('user.index')
                        ->with('message', __('User deleted successfully'));
    }

    /**
     * Show the user a form to change their personal information & password.
     *
     * @return \Inertia\Response
     */
    public function accountInfo()
    {
        $user = \Auth::user();

        return Inertia::render('Admin/User/AccountInfo', [
            'user' => $user,
        ]);
    }


    public function save_signature(Request $request)
    {
        $user_id = auth()->user()->id;
        $name = auth()->user()->name;

        $signaturePad = $request->signaturePad;

        if(!empty($signaturePad)){

            $user_update = User::find($user_id);

            $folderPath = public_path() . '/signature/user/';
            $image_parts = explode(";base64,", $signaturePad);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $fileName =  strtolower(str_replace(' ','_',date('d-m-Y').$user_id.'_'.$name.rand())).'.png';
            $file = $folderPath . $fileName;
            file_put_contents($file, $image_base64);

            $user_update->path_signature = asset('signature/user').'/'.$fileName;
            $user_update->img_signature = $fileName;

            $user_update->save();
            
        }

        return response()->json([
            'success' => true,
            'data'    => asset('signature/user').'/'.$fileName  
        ]);
    }

    /**
     * Save the modified personal information for a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accountInfoStore(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['max:15'],
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.\Auth::user()->id],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        // $user = \Auth::user()->update($request->except(['_token']));

        $user = User::find(auth()->user()->id);

        $user->name = $request->name;
        
        if(!empty($request->phone)){
            $user->phone = $request->phone;
        }

        $user->email = $request->email;

        $user->save();

        if ($user) {
            $message = 'Account updated successfully.';
        } else {
            $message = 'Error while saving. Please try again.';
        }
        $this->storeLogActivities("user change data info user account", auth()->user()->id);

        return redirect()->route('admin.account.info')->with('message', __($message));
    }

    /**
     * Save the new password for a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePasswordStore(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'old_password' => ['required'],
            'new_password' => ['required', Rules\Password::defaults()],
            'confirm_password' => ['required', 'same:new_password', Rules\Password::defaults()],
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($validator->failed()) {
                return;
            }
            if (! Hash::check($request->input('old_password'), \Auth::user()->password)) {
                $validator->errors()->add(
                    'old_password', __('Old password is incorrect.')
                );
            }
        });

        $validator->validate();

        $user = \Auth::user()->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        if ($user) {
            $message = 'Password updated successfully.';
        } else {
            $message = 'Error while saving. Please try again.';
        }

        $this->storeLogActivities("user change data pass user account", auth()->user()->id);

        return redirect()->route('admin.account.info')->with('message', __($message));
    }

    public function export() 
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }
}

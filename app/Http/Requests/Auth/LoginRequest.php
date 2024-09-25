<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Models\Employee\Param_employee;
use App\Models\Employee\Param_company;
use App\Models\Employee\Param_department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'email' => ['required', 'string', 'email'],
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        // $check_users = User::whereRaw("nip = '".$this->only('nip')['nip']."'")->first();
        // $employee = Param_employee::whereRaw("nip = '".$this->only('nip')['nip']."' AND is_active = 1")->first();

        // if(empty($check_users)){

        //     if(!empty($employee)){

        //         $company = Param_company::whereRaw("id = $employee->company_id")->first();
        //         $department = Param_department::whereRaw("id = $employee->department_id")->first();

        //         $new_user = new User();
        //         $new_user->name = $employee->name;
        //         $new_user->nip = $employee->nip;
        //         $new_user->email = $employee->email;
        //         $new_user->password = Hash::make(date('dmY',strtotime($employee->date_birthday)).'#');
        //         $new_user->phone = NULL;
        //         $new_user->is_active = 1;
        //         $new_user->company_id = $company->id;
        //         $new_user->company_name = $company->code;
        //         $new_user->department_id = $department->id;
        //         $new_user->department_name = $department->name;
        //         $new_user->save();

        //     }
        // }
        
        if (! Auth::attempt($this->only('nip', 'password'), $this->boolean('remember'))) {
            
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'nip' => trans('auth.failed'),
            ]);

        } else {

            if (Auth::user()->is_active != 1) {

                Auth::logout();

                throw ValidationException::withMessages([
                    'nip' => __('Account Suspend!'),
                ]);

            }

            // if(!empty($employee->department_id)){

            //     if(Auth::user()->department_id != $employee->department_id){

            //         $company = Param_company::whereRaw("id = $employee->company_id")->first();
            //         $department = Param_department::whereRaw("id = $employee->department_id")->first();
    
            //         $update_user = User::find(Auth::user()->id);
            //         $update_user->company_id = $company->id;
            //         $update_user->company_name = $company->code;
            //         $update_user->department_id = $department->id;
            //         $update_user->department_name = $department->name;
            //         $update_user->save();
    
            //     }
            // }
            
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'nip' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('nip')).'|'.$this->ip();
    }
}

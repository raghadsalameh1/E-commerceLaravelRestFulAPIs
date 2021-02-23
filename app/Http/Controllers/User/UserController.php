<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::all();
        return $this->ShowAll($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        /*$rules = [
          'name'=>'required',
          'email'=> 'required|email|unique:users',
          'password'=> 'required|min:6|confirmed'
        ];
        $this->validate($request,$rules);*/
        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token']=User::generateVerifecationCode();
        $data['admin']= User::REGULAR_USER;
        //$data['name'] = 'test';
        //$data['email']='est@email.com';

        $user = User::create($data);
        return $this->ShowOne( $user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //$user = User::findOrFail($id);
        return $this->ShowOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //$user = User::findOrFail($id);
        $rules = [
            'email' => 'email|unique:users,email,'.$user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:'.User::ADMIN_USER.','.User::REGULAR_USER,
        ];

        if($request->has('name'))
        {
          $user->name = $request->name;
        }

        if($request->has('email') && $user->email != $request->email)
        {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerifecationCode();
            $user->email = $request->email;
        }

        if($request->has('password'))
        {
          $user->password = bcrypt($request->password);
        }

        if($request->has('admin'))
        {
           if(!$user->IsVerified())
             return $this->errorResponse('only verified user can modify admin field',409);
           $user->admin = $request->admin;  
        }

        if(!$user->isDirty()){
            return $this->errorResponse('You need to specify different values to update', 422);
        }
        $user->save();
        return $this->ShowOne($user);
    }

    /**poui
     * Remove the specified resource from storage.
     *5roipoiuuuu
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //$user = User::findOrFail($id);
        $user->delete();
        return $this->successResponse('User is deleted successfully', 200);
    }
}

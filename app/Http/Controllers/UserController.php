<?php

namespace App\Http\Controllers;

use App\Apartment;
use App\MacAddress;
use App\Mail\WelcomeEmail;
use App\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        $apartments = Apartment::pluck('name', 'id');
        return view('Admin.User.view', compact('users', 'apartments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|email|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|max:255',
        ]);

        $user = User::create($request->all());
        $user->default_password = $request->get('password');
        $user->save();

        $user->radreplies()->create([
            'attribute' => 'Tunnel-Type',
            'value' => '13'
        ]);
        $user->radreplies()->create([
            'attribute' => 'Tunnel-Medium-Type',
            'value' => '6'
        ]);

        $user->radreplies()->create([
            'attribute' => 'Tunnel-Private-Group-Id',
            'value' => $user->apartment->vlan_id
        ]);

        return response('success');

    }

    /**
     * Display the specified resource.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->has('is_enabled')) {
            $request->validate([
                'is_enabled' => 'required'
            ]);
        } else {
            $request->validate([
                'apartment_id' => 'required',
                'name' => 'required|string|max:255',
                'email' => 'required|email|string|max:255',
                'username' => 'required|string|max:255',
            ]);
        }

        if (empty($request->password)) {
            $request->request->remove('password');
        }

        $user = User::findOrFail($id);
        $user->update($request->all());

        if (!$user->radreplies()->where('attribute', 'Tunnel-Type')->first()) {
            $user->radreplies()->create([
                'attribute' => 'Tunnel-Type',
                'value' => '13'
            ]);
        }

        if (!$user->radreplies()->where('attribute', 'Tunnel-Medium-Type')->first()) {
            $user->radreplies()->create([
                'attribute' => 'Tunnel-Medium-Type',
                'value' => '6'
            ]);
        }

        if (!$user->radreplies()->where('attribute', 'Tunnel-Private-Group-Id')->first()) {
            $user->radreplies()->create([
                'attribute' => 'Tunnel-Private-Group-Id',
                'value' => $user->apartment->vlan_id
            ]);
        } else {
            $user->radreplies()->where('attribute', 'Tunnel-Private-Group-Id')->first()->update([
                'value' => $user->apartment->vlan_id
            ]);
        }

        return response('success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        User::destroy($request->id);

        return response('Success');
    }

    public function macDestroy(Request $request)
    {
        MacAddress::where('user_id', '=', $request->user_id)
            ->where('is_permanent', '=', 0)
            ->delete();

        return response('Success');
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->password = $user->default_password;
        $user->save();
        return response('Success');
    }

    public function resetAll(Request $request)
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->password = $user->default_password;
            $user->save();
        }

        return response('Success');
    }

    public function sendWelcomeEmail(Request $request)
    {
        $user = User::find($request->user_id);
        Mail::to($user)->send(new WelcomeEmail($user));
        return 'Welcome Email sent to ' . $user->email;
    }

    public function getDataTable()
    {
        $users = User::with('apartment');

        return DataTables::of($users)
            ->addColumn('status', function ($user) {
                if ($user->is_enabled) {
                    return '<i class="la la-check-circle text-success">Active</i>';
                }
                return '<i class="la la-times-circle text-danger">Inactive</i>';
            })
            ->addColumn('action', function ($user) {
                if ($user->is_enabled) {
                    return '<button type="button" class="action btn btn-sm btn-danger" data-token="' . csrf_token() . '" data-id="' . $user->id . '" data-enabled="' . $user->is_enabled . '"><i class="la la-power-off"></button>';
                }
                return '<button type="button" class="action btn btn-sm btn-success" data-token="' . csrf_token() . '" data-id="' . $user->id . '" data-enabled="' . $user->is_enabled . '"><i class="la la-play"></i></button>';
            })
            ->addColumn('reset', function ($user) {
                return '<button type="button" class="reset btn btn-sm btn-warning" data-user-id="' . $user->id . '" data-token="' . csrf_token() . '">Reset</button>';
            })
            ->addColumn('reset_pwd', function ($user) {
                return '<button type="button" class="reset_pwd btn btn-sm btn-warning" data-user-id="' . $user->id . '" data-token="' . csrf_token() . '">Reset</button>';
            })
            ->addColumn('edit', function ($user) {
                return '<button type="button" class="edit btn btn-sm btn-primary" data-email="' . $user->email . '" data-apartment-id="' . $user->apartment_id . '" data-name="' . $user->name . '" data-username="' . $user->username . '" data-id="' . $user->id . '"><i class="la la-edit"></i></button>';
            })
            ->addColumn('delete', function ($user) {
                return '<button type="button" class="delete btn btn-sm btn-danger" data-delete-id="' . $user->id . '" data-token="' . csrf_token() . '" ><i class="la la-trash"></i></button>';
            })
            ->addColumn('send_email', function ($user) {
                return '<button type="button" class="send_email btn btn-sm btn-info" data-user-id="' . $user->id . '" data-token="' . csrf_token() . '" ><i class="la la-envelope"></i></button>';
            })
            ->rawColumns(['status', 'action', 'reset', 'reset_pwd', 'edit', 'delete', 'send_email'])
            ->make(true);
    }
}

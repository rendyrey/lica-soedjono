<?php

namespace App\Http\Controllers;

use DataTables;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Auth;

class UserManagementController extends Controller
{

    public function index()
    {
        $data['title'] = 'User List';
        return view('dashboard.user_management.index', $data);
    }

    public function datatable()
    {
        $model = \App\User::selectRaw('users.*')
            ->orderBy('name', 'asc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function create(Request $request)
    {
        DB::beginTransaction(); // begin of transaction
        try {
            $validator = \App\User::validate($request); // run validation of form of every master data
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $user = \App\User::where(['username' => $request->username])->first();

            if ($user) {
                DB::rollback(); // rollback the database if in the middle way of creation there is any error.
                return response()->json(['message' => 'Username is already exist']);
            } else {

                $createdData = DB::table('users')
                    ->insert([
                        'name' => $request->name,
                        'username' => $request->username,
                        'password' => Hash::make($request->password),
                        'role' => $request->role,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                $this->logActivity(
                    "Create user data with username $request->username",
                    json_encode($request->except(['_method', '_token']))
                );

                DB::commit(); // commit into DB if successfully created the data into masters.
                return response()->json(['message' => ucwords(str_replace("_", " ", 'create user')) . ' added successfully']);
            }
        } catch (\Exception $e) {
            DB::rollback(); // rollback the database if in the middle way of creation there is any error.
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }


    public function edit($id)
    {
        try {
            $data = \App\User::findOrFail($id);
            // $data = DB::table('users')->where('id', $id)->first();

            return $data;
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    public function update(Request $request)
    {

        DB::beginTransaction(); // begin of transaction database
        try {

            $updateData = [
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'updated_at' => Carbon::now()
            ];

            DB::table('users')
                ->where('id', $request->id)
                ->update($updateData);

            $this->logActivity(
                "Update users data with ID $request->id",
                json_encode($request->except(['_method', '_token']))
            );

            DB::commit();
            return response()->json(['message' => ucwords(str_replace("_", " ", 'users data')) . ' updated successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function delete($id)
    {
        try {
            $data = \App\User::findOrFail($id);
            $data->delete();

            $this->logActivity(
                "Delete users data with ID $id",
                json_encode($data)
            );

            return response()->json(['message' => ucwords('users data') . ' deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}

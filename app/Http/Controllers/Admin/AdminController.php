<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function list()
    {
        $data['getRecord'] = User::getRecord();
        return view('admin.kelolaakun', $data);
    }

    public function add()
    {
        return view('admin.add');
    }

    public function insert(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'id_tugas' => 'required|integer',
        ]);

        $user = new User;
        $user->role = 'user';
        $user->name = trim($request->name);
        $user->email = trim($request->email);
        $user->password = Hash::make($request->password);
        $user->id_tugas = trim($request->id_tugas);

        $user->save();

        return redirect('admin/kelolaakun')->with('success', "Akun berhasil dibuat.");
    }

    public function edit($id)
    {
        $data['getRecord'] = User::getSingle($id);
        return view('admin.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'id_tugas' => 'required|integer',
        ]);

        $user = User::find($id);
        $user->name = trim($request->name);
        $user->email = trim($request->email);
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->id_tugas = trim($request->id_tugas);

        $user->save();

        return redirect('admin/kelolaakun')->with('success', "Akun berhasil diupdate.");
    }

    public function delete($id)
    {
        $user = User::getSingle($id);
        $user->delete();

        return redirect('admin/kelolaakun')->with('success', "Akun berhasil dihapus.");
    }
}

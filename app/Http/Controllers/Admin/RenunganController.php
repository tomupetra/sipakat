<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Renungan;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class RenunganController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function listRenungan()
    {
        $data['getRecord'] = Renungan::getRecord();
        return view('renungan.list', $data);
    }

    public function tambahRenungan()
    {
        return view('renungan.tambah');
    }

    public function insertRenungan(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'ayat_harian' => 'required|string',
            'bacaan_pagi' => 'required|string|max:255',
            'bacaan_malam' => 'required|string|max:255',
            'lagu_ende' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $content = Purifier::clean($request->content);

        $renungan = new Renungan;
        $renungan->date = $request->date;
        $renungan->ayat_harian = $request->ayat_harian;
        $renungan->bacaan_pagi = $request->bacaan_pagi;
        $renungan->bacaan_malam = $request->bacaan_malam;
        $renungan->lagu_ende = $request->lagu_ende;
        $renungan->title = $request->title;
        $renungan->content = $request->content;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $renungan->image = $imageName;
        }

        $renungan->save();

        return redirect('admin/renungan/list')->with('success', "Renungan berhasil ditambahkan.");
    }

    public function showRenungan()
    {
        $renungan = Renungan::all();
        return view('landingpage.renungan', compact('renungan'));
    }

    public function lihatDetail($id)
    {
        $renungan = Renungan::findOrFail($id);
        return response()->json($renungan);
    }

    public function editRenungan($id)
    {
        $data['getRecord'] = Renungan::find($id);
        return view('renungan.edit', $data);
    }

    public function updateRenungan(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'ayat_harian' => 'required|string',
            'bacaan_pagi' => 'required|string|max:255',
            'bacaan_malam' => 'required|string|max:255',
            'lagu_ende' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $renungan = Renungan::find($id);
        $renungan->date = $request->date;
        $renungan->ayat_harian = $request->ayat_harian;
        $renungan->bacaan_pagi = $request->bacaan_pagi;
        $renungan->bacaan_malam = $request->bacaan_malam;
        $renungan->lagu_ende = $request->lagu_ende;
        $renungan->title = $request->title;
        $renungan->content = $request->content;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $renungan->image = $imageName;
        }

        $renungan->save();

        return redirect('admin/renungan/list')->with('success', "Renungan berhasil diubah.");
    }

    public function deleteRenungan($id)
    {
        $renungan = Renungan::getSingle($id);
        $renungan->delete();

        return redirect('admin/renungan/list')->with('success', "Renungan berhasil dihapus.");
    }

    public function detailRenungan($id)
    {
        $renungan = Renungan::findOrFail($id);
        return view('renungan.detail', compact('renungan'));
    }
}

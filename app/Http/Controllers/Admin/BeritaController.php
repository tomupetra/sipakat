<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index()
    {
        $berita = Berita::orderBy('date', 'desc')->paginate(12);
        return view('landingpage.berita', compact('berita'));
    }

    public function listBerita()
    {
        $data['getRecord'] = Berita::getRecord();
        return view('berita.list', $data);
    }

    public function tambahBerita()
    {
        return view('berita.tambah');
    }
    public function insertBerita(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $content = Purifier::clean($request->content);

        $berita = new Berita;
        $berita->date = $request->date;
        $berita->title = $request->title;
        $berita->content = $request->content;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $berita->image = $imageName;
        }

        $berita->save();

        return redirect('admin/berita')->with('success', "Berita berhasil ditambahkan.");
    }

    public function showBerita()
    {
        $berita = Berita::all();
    }

    public function editBerita($id)
    {
        $data['getRecord'] = Berita::getRecord($id);
        return view('berita.edit', $data);
    }

    public function updateBerita(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $content = Purifier::clean($request->content);

        $berita = Berita::find($id);
        $berita->date = $request->date;
        $berita->title = $request->title;
        $berita->content = $request->content;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $berita->image = $imageName;
        }

        $berita->save();

        return redirect('admin/berita')->with('success', "Berita berhasil diubah.");
    }

    public function deleteBerita($id)
    {
        $berita = Berita::find($id);
        $berita->delete();
        return redirect('admin/berita')->with('success', "Berita berhasil dihapus.");
    }

    public function detailBerita($id)
    {
        $berita = Berita::findOrFail($id);
        return response()->json($berita);
    }

    public function showBeritaLanding()
    {
        $berita = Berita::all();
        return view('landingpage.berita', compact('berita'));
    }

    public function showBeritaLandingDetail($id)
    {
        $data['getRecord'] = Berita::getRecord($id);
        return view('landingpage.berita_detail', $data);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warta;

class WartaController extends Controller
{
    public function index()
    {
        $data['getRecord'] = Warta::all();
        return view('warta.index', $data);
    }

    public function show($id)
    {
        $warta = Warta::findOrFail($id);
        return view('warta.show', compact('warta'));
    }

    public function create()
    {
        return view('warta.upload-warta');
    }

    public function uploadWarta(Request $request)
    {
        $request->validate([
            'warta' => 'required|mimes:pdf|max:1024',
        ]);

        if ($request->hasFile('warta')) {
            $file = $request->file('warta');
            $fileName = time() . '.' . $file->extension();
            $file->move(public_path('warta'), $fileName);

            Warta::create([
                'date' => $request->input('date'),
                'file_name' => $fileName,

            ]);
        }

        return redirect('admin/warta')->with('success', "Warta berhasil diupload.");
    }

    public function destroy($id)
    {
        $warta = Warta::findOrFail($id);
        $warta->delete();

        return redirect('admin/warta')->with('success', 'Warta telah dihapus.');
    }
}

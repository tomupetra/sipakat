<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        return view('gallery.index', [
            'images' => Gallery::all()
        ]);
    }

    public function uploadFoto(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $request->image->move(public_path('storage/images'), $imageName);

            Gallery::create([
                'image' => 'images/' . $imageName,
            ]);
        }

        return redirect()->back()->with('success', 'Foto berhasil diupload.');
    }

    public function delete($id)
    {
        $image = Gallery::find($id);
        $image->delete();

        return redirect()->route('admin.galeri')
            ->with('success', 'Image deleted successfully');
    }
}

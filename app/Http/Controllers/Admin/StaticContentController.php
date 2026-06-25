<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KontenIcon;
use App\Models\KontenAboutUs;
use App\Models\KontenSlideShow;

use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class StaticContentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:is-owner'),
        ];
    }

    public function index()
    {
        // Hanya ambil slideshow — halaman ini fokus ke slideshow saja
        $slides = KontenSlideShow::orderBy('urutan')->get();

        return view('admin.pages.kontenStatis', [
            'slides' => $slides,
            'activePage' => 'konten-statis',
        ]);
    }

    public function updateAbout(Request $request)
    {
        $data = $request->validate([
            'judul' => 'nullable|string|max:255',
            'isi' => 'nullable|string',
            'background_gambar' => 'nullable|image|max:10240',
            'tentang_gambar' => 'nullable|image|max:10240',
            'tentang_isi' => 'nullable|string',
            'visi_gambar' => 'nullable|image|max:10240',
            'visi_isi' => 'nullable|string',
            'nilai_gambar' => 'nullable|image|max:10240',
            'nilai_isi' => 'nullable|string',
        ]);

        $about = KontenAboutUs::first() ?? new KontenAboutUs();

        // handle multiple image uploads
        $imageFields = [
            'background_gambar', 'tentang_gambar', 'visi_gambar', 'nilai_gambar'
        ];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $name = time() . '_' . $field . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $file->getClientOriginalName());
                $file->move(public_path('assets/konten'), $name);
                $data[$field] = 'assets/konten/' . $name;
                if ($about->{$field} && file_exists(public_path($about->{$field}))) {
                    @unlink(public_path($about->{$field}));
                }
            }
        }

        $about->fill($data);
        $about->save();

        return back()->with('success', 'About Us updated');
    }

    public function updateIcon(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|integer|exists:konten_icon,id',
            'nama' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:1024',
            'urutan' => 'nullable|integer',
            'gambar' => 'nullable|image|max:5120',
            'delete_id' => 'nullable|integer|exists:konten_icon,id',
            // additional fields for logo and class icons
            'logo_gambar' => 'nullable|image|max:5120',
            'class_keranjang' => 'nullable|string|max:255',
            'class_favorit' => 'nullable|string|max:255',
            'class_rating' => 'nullable|string|max:255',
        ]);

        if ($request->filled('delete_id')) {
            $item = KontenIcon::find($request->input('delete_id'));
            if ($item) {
                if ($item->gambar && file_exists(public_path($item->gambar))) {
                    @unlink(public_path($item->gambar));
                }
                $item->delete();
            }
            return back()->with('success', 'Icon deleted');
        }

        // If the request contains class/icon specific fields, save them into special rows
        if ($request->filled('class_keranjang') || $request->filled('class_favorit') || $request->filled('class_rating') || $request->hasFile('logo_gambar')) {
            // logo
            $logo = KontenIcon::firstOrNew(['nama' => 'logo']);
            if ($request->hasFile('logo_gambar')) {
                $file = $request->file('logo_gambar');
                $name = time() . '_logo_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $file->getClientOriginalName());
                $file->move(public_path('assets/konten'), $name);
                if ($logo->gambar && file_exists(public_path($logo->gambar))) {
                    @unlink(public_path($logo->gambar));
                }
                $logo->gambar = 'assets/konten/' . $name;
                $logo->save();
            }

            // classes saved as entries with nama keys
            $keranjang = KontenIcon::firstOrNew(['nama' => 'class_keranjang']);
            $favorit = KontenIcon::firstOrNew(['nama' => 'class_favorit']);
            $rating = KontenIcon::firstOrNew(['nama' => 'class_rating']);

            if ($request->filled('class_keranjang')) { $keranjang->gambar = null; $keranjang->link = $request->input('class_keranjang'); $keranjang->save(); }
            if ($request->filled('class_favorit'))  { $favorit->gambar = null; $favorit->link = $request->input('class_favorit'); $favorit->save(); }
            if ($request->filled('class_rating'))   { $rating->gambar = null; $rating->link = $request->input('class_rating'); $rating->save(); }

            return back()->with('success', 'Icon settings saved');
        }

        // Fallback: regular CRUD for generic icon rows
        if ($request->filled('id')) {
            $item = KontenIcon::find($request->input('id'));
        } else {
            $item = new KontenIcon();
        }

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $name = time() . '_icon_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $file->getClientOriginalName());
            $file->move(public_path('assets/konten'), $name);
            $data['gambar'] = 'assets/konten/' . $name;
            if ($item->exists && $item->gambar && file_exists(public_path($item->gambar))) {
                @unlink(public_path($item->gambar));
            }
        }

        $item->fill(array_filter($data, function ($k) { return $k !== 'delete_id'; }, ARRAY_FILTER_USE_KEY));
        $item->save();

        return back()->with('success', 'Icon saved');
    }

    public function updateSlideshow(Request $request)
    {
        // Support banner1..3 fields for simplified slideshow management
        $data = $request->validate([
            'banner1_name' => 'nullable|string|max:255',
            'banner1_gambar' => 'nullable|image|max:10240',
            'banner2_name' => 'nullable|string|max:255',
            'banner2_gambar' => 'nullable|image|max:10240',
            'banner3_name' => 'nullable|string|max:255',
            'banner3_gambar' => 'nullable|image|max:10240',
        ]);

        for ($i = 1; $i <= 3; $i++) {
            $nameKey = "banner{$i}_name";
            $imgKey = "banner{$i}_gambar";
            $item = KontenSlideShow::firstOrNew(['urutan' => $i]);
            if ($request->filled($nameKey)) {
                $item->judul = $request->input($nameKey);
            }
            if ($request->hasFile($imgKey)) {
                $file = $request->file($imgKey);
                $name = time() . '_slide' . $i . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $file->getClientOriginalName());
                $file->move(public_path('assets/konten'), $name);
                if ($item->gambar && file_exists(public_path($item->gambar))) { @unlink(public_path($item->gambar)); }
                $item->gambar = 'assets/konten/' . $name;
            }
            $item->urutan = $i;
            $item->save();
        }

        return back()->with('success', 'Slides aktualisasi disimpan');
    }

    // API endpoints for slideshow CRUD used by the admin UI (AJAX)
    public function storeSlide(Request $request)
    {
        $data = $request->validate([
            'judul' => 'nullable|string|max:255',
            'gambar' => 'required|image|max:10240',
        ]);

        $file = $request->file('gambar');
        $name = time() . '_slide_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $file->getClientOriginalName());
        $file->move(public_path('assets/konten'), $name);

        $slide = new KontenSlideShow();
        $slide->judul = $data['judul'] ?? null;
        $slide->gambar = 'assets/konten/' . $name;
        $last = KontenSlideShow::orderBy('urutan', 'desc')->first();
        $slide->urutan = $last ? ($last->urutan + 1) : 1;
        $slide->aktif = true;
        $slide->save();

        return response()->json(['success' => true, 'slide' => $slide]);
    }

    public function updateSlide(Request $request, $id)
    {
        $slide = KontenSlideShow::findOrFail($id);

        $data = $request->validate([
            'judul' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $name = time() . '_slide_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $file->getClientOriginalName());
            $file->move(public_path('assets/konten'), $name);
            if ($slide->gambar && file_exists(public_path($slide->gambar))) { @unlink(public_path($slide->gambar)); }
            $slide->gambar = 'assets/konten/' . $name;
        }

        if (isset($data['judul'])) { $slide->judul = $data['judul']; }
        $slide->save();

        return response()->json(['success' => true, 'slide' => $slide]);
    }

    public function deleteSlide(Request $request, $id)
    {
        $slide = KontenSlideShow::findOrFail($id);
        if ($slide->gambar && file_exists(public_path($slide->gambar))) { @unlink(public_path($slide->gambar)); }
        $slide->delete();

        // reindex urutan
        $slides = KontenSlideShow::orderBy('urutan')->get();
        $i = 1; foreach ($slides as $s) { $s->urutan = $i++; $s->save(); }

        return response()->json(['success' => true]);
    }
}

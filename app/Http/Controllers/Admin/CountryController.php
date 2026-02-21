<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CountryController extends Controller
{
    /**
     * Davlatlar ro'yxati
     */
    public function index()
    {
        $countries = Country::withCount('conferences')->orderBy('name')->get();
        return view('admin.countries.index', compact('countries'));
    }

    /**
     * Yangi davlat qo'shish formasi
     */
    public function create()
    {
        return view('admin.countries.create');
    }

    /**
     * Yangi davlat saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'conference_name' => 'nullable|string|max:500',
            'conference_description' => 'nullable|string',
            'code' => 'required|string|size:3|unique:countries,code',
            'flag' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'cover_image' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
            'is_active' => 'nullable',
        ]);

        // Bayroq yuklash
        if ($request->hasFile('flag')) {
            $validated['flag_url'] = $request->file('flag')->store('flags', 'public');
        }

        // Cover rasm yuklash
        if ($request->hasFile('cover_image')) {
            $coverPath = 'images/countries/' . strtolower($validated['code']) . '.png';
            $request->file('cover_image')->move(public_path('images/countries'), strtolower($validated['code']) . '.png');
            $validated['cover_image'] = $coverPath;
        }

        $validated['is_active'] = $request->has('is_active');

        // Default konferensiya nomi
        if (empty($validated['conference_name'])) {
            $validated['conference_name'] = 'Bu yerda konferensiya nomi yoziladi';
        }

        Country::create($validated);

        return redirect()->route('admin.countries.index')
            ->with('success', 'Davlat muvaffaqiyatli qo\'shildi.');
    }

    /**
     * Davlatni ko'rish
     */
    public function show(Country $country)
    {
        $country->load([
            'conferences' => function ($query) {
                $query->withCount('articles')->latest();
            }
        ]);
        return view('admin.countries.show', compact('country'));
    }

    /**
     * Davlatni tahrirlash formasi
     */
    public function edit(Country $country)
    {
        return view('admin.countries.edit', compact('country'));
    }

    /**
     * Davlatni yangilash
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'conference_name' => 'nullable|string|max:500',
            'conference_description' => 'nullable|string',
            'code' => 'required|string|size:3|unique:countries,code,' . $country->id,
            'flag' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'cover_image' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
            'is_active' => 'nullable',
        ]);

        // Bayroq yuklash
        if ($request->hasFile('flag')) {
            // Eski rasmni o'chirish
            if ($country->flag_url) {
                Storage::disk('public')->delete($country->flag_url);
            }
            $validated['flag_url'] = $request->file('flag')->store('flags', 'public');
        }

        // Cover rasm yuklash
        if ($request->hasFile('cover_image')) {
            $coverPath = 'images/countries/' . strtolower($country->code) . '.png';
            $request->file('cover_image')->move(public_path('images/countries'), strtolower($country->code) . '.png');
            $validated['cover_image'] = $coverPath;
        }

        $validated['is_active'] = $request->has('is_active');

        $country->update($validated);

        return redirect()->route('admin.countries.index')
            ->with('success', 'Davlat muvaffaqiyatli yangilandi.');
    }

    /**
     * Davlatni o'chirish
     */
    public function destroy(Country $country)
    {
        if ($country->flag_url) {
            Storage::disk('public')->delete($country->flag_url);
        }

        $country->delete();

        return redirect()->route('admin.countries.index')
            ->with('success', 'Davlat muvaffaqiyatli o\'chirildi.');
    }

    /**
     * Konferensiya nomini AJAX orqali yangilash
     */
    public function updateConferenceName(Request $request, Country $country)
    {
        $validated = $request->validate([
            'conference_name' => 'nullable|string|max:500',
        ]);

        $country->update([
            'conference_name' => $validated['conference_name'] ?: null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Konferensiya nomi yangilandi',
            'conference_name' => $country->conference_name
        ]);
    }
}

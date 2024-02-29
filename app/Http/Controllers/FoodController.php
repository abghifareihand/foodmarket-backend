<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodRequest;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $food = Food::paginate(10);

        return view('food.index', [
            'food' => $food,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('food.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FoodRequest $request)
    {
        $data = $request->all();

        // Simpan gambar dan dapatkan pathnya
        $picturePath = $request->file('picturePath')->store('assets/food', 'public');

        // Mendapatkan URL gambar
        $url = url('storage/' . $picturePath);

        // Tambahkan URL gambar ke data
        $data['picturePath'] = $url;

        // Buat entri baru dalam database
        Food::create($data);

        // Redirect ke halaman index
        return redirect()->route('food.index');
    }


    /**
     * Display the specified resource.
     */
    public function show(Food $food)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Food $food)
    {
        return view('food.edit', [
            'item' => $food
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FoodRequest $request, Food $food)
    {
        $data = $request->all();

        // Periksa apakah ada file gambar baru yang diunggah
        if ($request->hasFile('picturePath')) {
            // Jika ada, simpan file gambar baru dan dapatkan pathnya
            $picturePath = $request->file('picturePath')->store('assets/food', 'public');

            // Dapatkan URL gambar baru
            $url = url('storage/' . $picturePath);

            // Tambahkan URL gambar baru ke data
            $data['picturePath'] = $url;
        }

        // Update data makanan dengan data yang baru
        $food->update($data);

        // Redirect ke halaman indeks makanan
        return redirect()->route('food.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Food $food)
    {
        $food->delete();

        return redirect()->route('food.index');
    }
}

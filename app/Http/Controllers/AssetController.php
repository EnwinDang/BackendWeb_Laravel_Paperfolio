<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::all();
        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'symbol' => 'required|string|unique:assets,symbol',
        ]);

        Asset::create([
            'name' => $request->name,
            'symbol' => strtoupper($request->symbol),
        ]);

        return redirect()->route('assets.index');
    }
}

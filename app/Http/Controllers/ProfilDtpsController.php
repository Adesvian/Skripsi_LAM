<?php

namespace App\Http\Controllers;

use App\Models\ProfilDtps;
use Illuminate\Http\Request;

class ProfilDtpsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ProfilDtps::all(); // Ambil semua data
        
    return view('/profil-dtps/create', compact('create'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Profil DTPS';
        return view('profil-dtps.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'nama_dosen_dtps' => 'required|string|max:255',
            'nidn' => 'required|string|max:20|unique:profil_dtps',
            'ttl' => 'required|date',
            'bukti_sertifikasi' => 'required|file|mimes:pdf,jpg,png|max:5048',
        ]);
        $filePath = $request->file('bukti_sertifikasi')->store('sertifikatDTPS', 'public');
        ProfilDtps::create([
            'nama_dosen_dtps' => $request->nama_dosen_dtps,
            'nidn' => $request->nidn,
            'tanggal_lahir' => $request->ttl,
            'bukti_sertifikasi' => $filePath,
        ]);
        
        return redirect()->route('profil-dtps.create')->with('success', 'Data dosen berhasil ditambahkan!');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(ProfilDtps $profilDtps)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProfilDtps $profilDtps)
    {
        return view('profil-dtps.edit', compact('profilDtps'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Fetch the existing record
        $profilDtps = ProfilDtps::findOrFail($id);

        // Validate only provided fields
        $validatedData = $request->validate([
            'nama_dosen_dtps' => 'sometimes|required|string|max:255',
            'nidn' => 'sometimes|required|string|max:20|unique:profil_dtps,nidn,' . $id,
            'ttl' => 'sometimes|required|date',
            'bukti_sertifikasi' => 'nullable|file|mimes:pdf,jpg,png|max:5048',
        ]);

        // Handle file upload only if a new file is provided
        if ($request->hasFile('bukti_sertifikasi')) {
            $validatedData['bukti_sertifikasi'] = $request->file('bukti_sertifikasi')->store('sertifikatDTPS', 'public');
        } else {
            // Keep old file if no new file is uploaded
            $validatedData['bukti_sertifikasi'] = $profilDtps->bukti_sertifikasi;
        }

        // Merge old data with new values (keep old if not in request)
        $profilDtps->update([
            'nama_dosen_dtps' => $validatedData['nama_dosen_dtps'] ?? $profilDtps->nama_dosen_dtps,
            'nidn' => $validatedData['nidn'] ?? $profilDtps->nidn,
            'tanggal_lahir' => $validatedData['ttl'] ?? $profilDtps->tanggal_lahir,
            'bukti_sertifikasi' => $validatedData['bukti_sertifikasi'],
        ]);

        return redirect()->route('profil-dtps.create')->with('success', 'Data dosen berhasil diperbarui!');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $id = ProfilDtps::where('id', '=', $id)->delete();
        return redirect()->back()->with('success', 'Post deleted successfully.');
    }
}

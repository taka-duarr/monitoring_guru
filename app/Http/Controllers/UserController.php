<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $jabatan = $request->query('jabatan');
        $status = $request->query('status');

        $query = User::query()->with('kelas');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }

        if ($jabatan) {
            $query->where('jabatan', $jabatan);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $data = $query->orderBy('name', 'asc')->paginate(15)->appends($request->query());
        $activeFilterCount = collect([$search, $jabatan, $status])->filter()->count();

        return view('admin.users.index', compact('data', 'activeFilterCount', 'search', 'jabatan', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::orderBy('name', 'asc')->get();
        return view('admin.users.form', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:50|unique:users,nik',
            'jabatan' => 'required|string|in:admin,guru,ketuakelas',
            'status' => 'required|string|in:Aktif,Cuti,Pensiun',
            'password' => 'required|string|min:4|confirmed',
            'kelas_id' => 'required_if:jabatan,ketuakelas|nullable|exists:kelas,id',
            'jenis_kelamin' => 'nullable|string|in:L,P',
            'no_telp' => 'nullable|string|max:20',
        ]);

        $data = $request->only([
            'name', 'nik', 'jabatan', 'status', 'kelas_id', 'jenis_kelamin', 'no_telp'
        ]);

        $data['password'] = Hash::make($request->password);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'Akun berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return redirect()->route('users.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = User::findOrFail($id);
        $kelas = Kelas::orderBy('name', 'asc')->get();
        return view('admin.users.form', compact('data', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'nik')->ignore($user->id),
            ],
            'jabatan' => 'required|string|in:admin,guru,ketuakelas',
            'status' => 'required|string|in:Aktif,Cuti,Pensiun',
            'password' => 'nullable|string|min:4|confirmed',
            'kelas_id' => 'required_if:jabatan,ketuakelas|nullable|exists:kelas,id',
            'jenis_kelamin' => 'nullable|string|in:L,P',
            'no_telp' => 'nullable|string|max:20',
        ]);

        $data = $request->only([
            'name', 'nik', 'jabatan', 'status', 'kelas_id', 'jenis_kelamin', 'no_telp'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle case where role changes from ketuakelas to something else
        if ($data['jabatan'] !== 'ketuakelas') {
            $data['kelas_id'] = null;
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting oneself
        if ($user->id === \Illuminate\Support\Facades\Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Akun berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function index()
    {
        $page = (object) ['title' => 'Profil Anda'];
        $breadcrumb = (object) [
            'title' => 'Profil',
            'list' => ['Home']
        ];

        $activeMenu = 'profil';

        return view('profil.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function upload_photo(Request $request)
    {
        try {
            $request->validate([
                'foto_profil' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $user = UserModel::find(Auth::id());

            if (!$user) return redirect()->back()->with('error', 'User tidak ditemukan.');

            if ($request->hasFile('foto_profil')) {
                $file = $request->file('foto_profil');
                $filename = 'profile-' . $user->user_id . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/profile-photo', $filename);

                $user->foto_profil = $filename;
                $user->save();
            }

            return redirect()->back()->with('success', 'Foto profil berhasil diunggah.');
        } catch (Exception $exception) {
            return redirect()->back()->with('error', 'Gagal mengunggah foto profil.');
        }
    }
}

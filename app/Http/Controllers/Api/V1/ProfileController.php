<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user   = $request->user();
        $daerah = $user->daerah;

        return response()->json([
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'username' => $user->username,
            'daerah'   => [
                'id'        => $daerah->id,
                'name'      => $daerah->name,
                'pic_name'  => $daerah->pic_name,
                'pic_phone' => $daerah->pic_phone,
                'address'   => $daerah->address,
                'phone'     => $daerah->phone,
                'is_active' => $daerah->is_active,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'address' => 'nullable|string|max:500',
            'phone'   => 'nullable|string|max:20',
        ]);

        $daerah = $request->user()->daerah;
        $daerah->update($request->only('address', 'phone'));

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'daerah'  => $daerah->fresh(),
        ]);
    }
}

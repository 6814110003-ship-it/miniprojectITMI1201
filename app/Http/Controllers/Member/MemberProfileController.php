<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MemberProfileController extends Controller
{
    public function edit(): View
    {
        $member = Auth::guard('web')->user();

        return view('member.profile', compact('member'));
    }

    public function update(Request $request): RedirectResponse
    {
        $member = Auth::guard('web')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:members,email,'.$member->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = $data['password'];
        } else {
            unset($data['password']);
        }

        $member->update($data);

        return back()->with('success', 'อัปเดตโปรไฟล์เรียบร้อย');
    }
}
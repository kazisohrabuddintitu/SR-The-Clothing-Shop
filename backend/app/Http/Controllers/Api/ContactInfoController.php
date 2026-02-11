<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactInfo;
use Illuminate\Http\Request;

class ContactInfoController extends Controller
{
    public function show()
    {
        $info = ContactInfo::query()->first();

        if (! $info) {
            $info = ContactInfo::create([
                'email' => config('app.contact_email', 'sr@gmail.com'),
                'phone' => null,
                'address' => null,
                'hours' => null,
            ]);
        }

        return response()->json(['contact' => $info]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'hours' => ['nullable', 'string', 'max:255'],
        ]);

        $info = ContactInfo::query()->first();
        if (! $info) {
            $info = ContactInfo::create($data);
        } else {
            $info->update($data);
        }

        return response()->json(['contact' => $info]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\ContactMessageRequest;
use App\Models\ContactInfo;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(ContactMessageRequest $request)
    {
        $data = $request->validated();
        $contactEmail = ContactInfo::query()->value('email')
            ?? config('app.contact_email', 'sr@gmail.com');

        Mail::raw(
            "Name: {$data['name']}\nEmail: {$data['email']}\n\nMessage:\n{$data['message']}",
            function ($message) use ($data, $contactEmail) {
                $message->to($contactEmail)
                    ->subject('SR Contact Message')
                    ->replyTo($data['email'], $data['name']);
            }
        );

        return response()->json(['message' => 'Message sent.']);
    }
}

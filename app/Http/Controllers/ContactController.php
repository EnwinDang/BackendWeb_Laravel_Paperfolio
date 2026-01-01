<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact.show');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $admins = User::where('is_admin', true)->get();

        foreach ($admins as $admin) {
            Mail::raw(
                "Name: {$request->name}\n" .
                "Email: {$request->email}\n" .
                "Subject: {$request->subject}\n\n" .
                "Message:\n{$request->message}",
                function ($message) use ($admin, $request) {
                    $message->to($admin->email)
                        ->subject('Contact Form: ' . $request->subject);
                }
            );
        }

        return redirect()->route('contact.show')->with('success', 'Your message has been sent successfully.');
    }
}

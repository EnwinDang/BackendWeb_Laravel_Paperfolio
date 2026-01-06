<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        // Only allow regular users (not admins) to access the contact form
        if (auth()->check() && auth()->user()->is_admin) {
            abort(403, 'Admins cannot access the contact form.');
        }
        
        return view('contact.show');
    }

    public function submit(Request $request)
    {
        // Only allow regular users (not admins) to submit the contact form
        if (auth()->check() && auth()->user()->is_admin) {
            abort(403, 'Admins cannot submit the contact form.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Store the submission in the database
        ContactSubmission::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'read' => false,
        ]);

        // Send email to all admins
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

    public function index()
    {
        $submissions = ContactSubmission::orderBy('created_at', 'desc')->get();
        return view('contact.index', compact('submissions'));
    }

    public function showSubmission(ContactSubmission $contactSubmission)
    {
        // Mark as read when viewing
        if (!$contactSubmission->read) {
            $contactSubmission->update(['read' => true]);
        }
        
        return view('contact.show-submission', compact('contactSubmission'));
    }

    public function markAsRead(ContactSubmission $contactSubmission)
    {
        $contactSubmission->update(['read' => true]);
        return back()->with('success', 'Submission marked as read.');
    }

    public function markAsUnread(ContactSubmission $contactSubmission)
    {
        $contactSubmission->update(['read' => false]);
        return back()->with('success', 'Submission marked as unread.');
    }

    public function destroy(ContactSubmission $contactSubmission)
    {
        $contactSubmission->delete();
        return redirect()->route('contact.index')->with('success', 'Submission deleted successfully.');
    }
}

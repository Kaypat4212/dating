<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PageController extends Controller
{
    public function contact(): View
    {
        return view('pages.contact');
    }

    public function contactSubmit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $to = SiteSetting::get('legal_support_email')
            ?: SiteSetting::get('footer_support_email')
            ?: config('mail.from.address', 'support@heartsconnect.com');

        Mail::raw(
            "Contact form submission from {$data['name']} <{$data['email']}>\n\nSubject: {$data['subject']}\n\n{$data['message']}",
            function ($mail) use ($data, $to) {
                $mail->to($to)
                     ->replyTo($data['email'], $data['name'])
                     ->subject('[Contact] ' . $data['subject']);
            }
        );

        return back()->with('success', "Thanks {$data['name']}! We've received your message and will get back to you shortly.");
    }

    public function helpCenter(): View
    {
        return view('pages.help-center');
    }

    public function safetyTips(): View
    {
        return view('pages.safety-tips');
    }

    public function reportAbuse(): View
    {
        return view('pages.report-abuse');
    }

    public function cookieSettings(): View
    {
        return view('pages.cookie-settings');
    }
}

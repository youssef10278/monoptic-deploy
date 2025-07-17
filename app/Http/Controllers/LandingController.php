<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('landing');
    }

    /**
     * Handle WhatsApp redirect for demo requests.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestDemo(Request $request)
    {
        $whatsappNumber = '212600400436';
        $message = 'Bonjour, je souhaite plus d\'informations concernant MONOPTI';
        
        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);
        
        return redirect()->away($whatsappUrl);
    }

    /**
     * Handle contact form submission (if needed later).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function contact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:1000',
        ]);

        // Here you can save to database, send email, etc.
        // For now, we'll just return success
        
        return response()->json([
            'success' => true,
            'message' => 'Votre message a été envoyé avec succès. Nous vous contacterons bientôt!'
        ]);
    }
}

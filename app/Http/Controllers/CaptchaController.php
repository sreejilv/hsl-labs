<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CaptchaController extends Controller
{
    public function generate()
    {
        // Generate random captcha text
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $captcha_text = '';
        for ($i = 0; $i < 5; $i++) {
            $captcha_text .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // Store in session
        session(['captcha_text' => $captcha_text]);
        
        // Create image
        $width = 150;
        $height = 60;
        $image = imagecreate($width, $height);
        
        // Colors
        $bg_color = imagecolorallocate($image, 245, 245, 245);
        $text_color = imagecolorallocate($image, 50, 50, 50);
        $line_color = imagecolorallocate($image, 200, 200, 200);
        $noise_color = imagecolorallocate($image, 150, 150, 150);
        
        // Fill background
        imagefill($image, 0, 0, $bg_color);
        
        // Add noise dots
        for ($i = 0; $i < 50; $i++) {
            imagesetpixel($image, rand(0, $width), rand(0, $height), $noise_color);
        }
        
        // Add distraction lines
        for ($i = 0; $i < 5; $i++) {
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $line_color);
        }
        
        // Add text with slight rotation and positioning
        $font_size = 16;
        $x = 20;
        
        for ($i = 0; $i < strlen($captcha_text); $i++) {
            $angle = rand(-15, 15);
            $y = rand(35, 45);
            $char_color = imagecolorallocate($image, rand(30, 100), rand(30, 100), rand(30, 100));
            
            imagestring($image, $font_size, $x, $y - 20, $captcha_text[$i], $char_color);
            $x += 25;
        }
        
        // Output image
        ob_start();
        imagepng($image);
        $image_data = ob_get_contents();
        ob_end_clean();
        
        imagedestroy($image);
        
        return response($image_data)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
    
    public function verify(Request $request)
    {
        $user_input = strtoupper(trim($request->input('captcha')));
        $session_captcha = strtoupper(session('captcha_text'));
        
        return response()->json([
            'valid' => $user_input === $session_captcha
        ]);
    }
}

<?php

/**
 * ======================================================
 * KONFIGURASI GEMINI AI API
 * ======================================================
 * 
 * Cara mendapatkan API Key:
 * 1. Buka https://aistudio.google.com/apikey
 * 2. Login dengan akun Google
 * 3. Klik "Create API Key"
 * 4. Salin key dan tempelkan di bawah ini
 * 
 * PENTING: Jangan share API key ini ke publik!
 * ======================================================
 */

return [

    // API Key Gemini (WAJIB DIISI)
    'api_key' => 'ISI_API_KEY_GEMINI_ANDA_DISINI',

    // Model yang digunakan (Gemini 2.0 Flash - gratis & cepat)
    'model' => 'gemini-2.0-flash',

    // Endpoint base URL
    'base_url' => 'https://generativelanguage.googleapis.com/v1beta/models',

    // Pengaturan generasi
    'temperature' => 0.7,       // Kreativitas (0.0 = presisi, 1.0 = kreatif)
    'max_output_tokens' => 8192, // Maksimum token output

    // Timeout cURL (detik)
    'timeout' => 60,

];

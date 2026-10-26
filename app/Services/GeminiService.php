<?php

namespace App\Services;

use App\Core\Database;

/**
 * ======================================================
 * GeminiService - Layanan Integrasi AI (Gemini & Groq)
 * ======================================================
 * 
 * Service class untuk berkomunikasi dengan API AI
 * menggunakan PHP native cURL.
 * ======================================================
 */
class GeminiService {

    private $geminiKey;
    private $groqKey;
    private $model;
    private $temperature;
    private $maxTokens;
    private $timeout;

    /** @var string|null Error terakhir jika request gagal */
    private $lastError = null;

    /** @var string|null Raw response body dari API */
    private $lastRawResponse = null;

    public function __construct() {
        $config = require __DIR__ . '/../../config/gemini.php';
        
        $this->temperature = $config['temperature'] ?? 0.7;
        $this->timeout     = $config['timeout'] ?? 60;
        
        try {
            $db = Database::connect('core');
            $settings = $db->query("SELECT * FROM ai_settings LIMIT 1")->fetch();
            if ($settings) {
                $this->geminiKey = $settings['gemini_api_key'] ?? '';
                $this->groqKey   = $settings['groq_api_key'] ?? '';
                $this->model     = $settings['model'] ?? 'gemini-1.5-flash';
                $this->maxTokens = (int)($settings['max_tokens'] ?? 2048);
            }
        } catch (\Exception $e) {}
    }

    private function isGroqModel(): bool {
        return strpos($this->model, 'llama') !== false || strpos($this->model, 'mixtral') !== false || strpos($this->model, 'gemma') !== false;
    }

    /**
     * Cek apakah API Key sudah dikonfigurasi
     */
    public function isConfigured(): bool {
        if ($this->isGroqModel()) {
            return !empty($this->groqKey);
        }
        return !empty($this->geminiKey) && $this->geminiKey !== 'ISI_API_KEY_GEMINI_ANDA_DISINI';
    }



    /**
     * Getter untuk error terakhir
     */
    public function getLastError(): ?string {
        return $this->lastError;
    }

    /**
     * Getter untuk raw response terakhir
     */
    public function getLastRawResponse(): ?string {
        return $this->lastRawResponse;
    }

    // ─────────────────────────────────────────────────────────
    //  PROMPT BUILDER
    // ─────────────────────────────────────────────────────────

    /**
     * Bangun prompt AI berdasarkan parameter input guru.
     * Prompt ini dirancang agar Gemini menghasilkan output
     * berformat JSON yang konsisten dan siap parse.
     *
     * @param array $params Parameter dari form input
     * @return string Prompt final yang dikirim ke Gemini
     */
    public function buildPrompt(array $params): string {

        $mapelNama   = $params['mapel_nama']   ?? 'Umum';
        $kelasNama   = $params['kelas_nama']   ?? '-';
        $materi      = $params['materi']       ?? '-';
        $tipeSoal    = $params['tipe_soal']     ?? 'PG';
        $kesulitan   = $params['tingkat_kesulitan'] ?? 'Sedang';
        $bloom       = $params['level_bloom']  ?? 'C3, C4';
        $jumlah      = (int)($params['jumlah_soal'] ?? 5);

        // Deskripsi tipe soal untuk prompt
        $tipeDeskripsi = $this->getTipeSoalDeskripsi($tipeSoal);

        // Instruksi format JSON berbeda per tipe soal
        $formatInstruksi = $this->getFormatInstruksi($tipeSoal);

        $prompt = <<<PROMPT
Kamu adalah seorang guru profesional di Indonesia yang ahli dalam membuat soal ujian berkualitas tinggi berdasarkan Kurikulum Merdeka.

═══ TUGAS ═══
Buatkan {$jumlah} soal {$tipeDeskripsi} untuk keperluan ujian/evaluasi dengan parameter berikut:

- Mata Pelajaran : {$mapelNama}
- Kelas / Jenjang: {$kelasNama}
- Materi Pokok   : {$materi}
- Tingkat Kesulitan: {$kesulitan}
- Level Taksonomi Bloom Target: {$bloom}

═══ ATURAN PEMBUATAN SOAL ═══
1. Soal harus ORIGINAL, tidak boleh copy-paste dari internet.
2. Soal harus sesuai dengan level kognitif Bloom yang diminta ({$bloom}).
3. Setiap soal harus memiliki pembahasan yang jelas dan edukatif.
4. Bahasa harus baku, jelas, tidak ambigu, sesuai KBBI dan EYD.
5. Tingkat kesulitan harus sesuai: {$kesulitan}.
6. Soal harus kontekstual dan relevan dengan kehidupan sehari-hari siswa Indonesia.
7. Distribusikan level kognitif secara merata dari level yang diminta.
{$formatInstruksi}

═══ FORMAT OUTPUT ═══
Kembalikan HANYA JSON array murni (tanpa markdown, tanpa ```json```, tanpa penjelasan tambahan). 
Formatnya adalah array JSON seperti contoh berikut:

{$this->getContohJson($tipeSoal)}

PENTING:
- Output HANYA berisi JSON array valid. Tidak ada teks lain sebelum atau sesudah JSON.
- Pastikan JSON valid dan bisa di-parse oleh json_decode PHP.
- Jumlah item dalam array HARUS tepat {$jumlah}.
- Field "level_kognitif" diisi salah satu dari: C1, C2, C3, C4, C5, C6.
PROMPT;

        return $prompt;
    }

    /**
     * Deskripsi tipe soal dalam bahasa Indonesia untuk prompt
     */
    private function getTipeSoalDeskripsi(string $tipe): string {
        $map = [
            'PG'            => 'Pilihan Ganda (Multiple Choice) dengan 5 opsi jawaban (A-E)',
            'ESSAY'         => 'Essay / Uraian',
            'BENAR_SALAH'   => 'Benar atau Salah (True/False)',
            'ISIAN_SINGKAT' => 'Isian Singkat (Short Answer)',
        ];
        return $map[$tipe] ?? 'Pilihan Ganda';
    }

    /**
     * Instruksi format spesifik per tipe soal
     */
    private function getFormatInstruksi(string $tipe): string {
        switch ($tipe) {
            case 'PG':
                return <<<INST
8. Untuk Pilihan Ganda: buat 5 opsi jawaban (A sampai E).
9. Pastikan pengecoh (distractor) masuk akal dan tidak mudah ditebak.
10. Opsi jawaban harus homogen (sejenis) agar tidak ada yang terlalu mencolok.
INST;
            case 'ESSAY':
                return <<<INST
8. Untuk soal Essay: buat pertanyaan terbuka yang menuntut jawaban uraian.
9. Kunci jawaban berisi jawaban ideal yang lengkap dan terstruktur.
10. Pembahasan berisi poin-poin kunci yang harus ada dalam jawaban siswa.
INST;
            case 'BENAR_SALAH':
                return <<<INST
8. Untuk soal Benar/Salah: buat pernyataan yang jelas benar atau salah.
9. Kunci jawaban diisi "Benar" atau "Salah".
10. Hindari pernyataan yang ambigu atau bisa ditafsirkan ganda.
INST;
            case 'ISIAN_SINGKAT':
                return <<<INST
8. Untuk Isian Singkat: buat pertanyaan yang jawabannya singkat (1-5 kata).
9. Kunci jawaban berisi jawaban singkat yang tepat.
10. Hindari pertanyaan yang bisa dijawab dengan banyak variasi kata berbeda.
INST;
            default:
                return '';
        }
    }

    /**
     * Contoh JSON output per tipe soal untuk panduan AI
     */
    private function getContohJson(string $tipe): string {
        switch ($tipe) {
            case 'PG':
                return <<<JSON
[
  {
    "pertanyaan": "Teks soal di sini...",
    "opsi_a": "Opsi jawaban A",
    "opsi_b": "Opsi jawaban B",
    "opsi_c": "Opsi jawaban C",
    "opsi_d": "Opsi jawaban D",
    "opsi_e": "Opsi jawaban E",
    "kunci_jawaban": "B",
    "pembahasan": "Penjelasan mengapa B adalah jawaban yang benar...",
    "level_kognitif": "C3"
  }
]
JSON;
            case 'ESSAY':
                return <<<JSON
[
  {
    "pertanyaan": "Teks soal essay di sini...",
    "opsi_a": null,
    "opsi_b": null,
    "opsi_c": null,
    "opsi_d": null,
    "opsi_e": null,
    "kunci_jawaban": "Jawaban ideal yang lengkap dan terstruktur...",
    "pembahasan": "Poin-poin kunci yang harus ada dalam jawaban siswa...",
    "level_kognitif": "C4"
  }
]
JSON;
            case 'BENAR_SALAH':
                return <<<JSON
[
  {
    "pertanyaan": "Pernyataan benar atau salah di sini...",
    "opsi_a": "Benar",
    "opsi_b": "Salah",
    "opsi_c": null,
    "opsi_d": null,
    "opsi_e": null,
    "kunci_jawaban": "Benar",
    "pembahasan": "Penjelasan mengapa pernyataan ini benar/salah...",
    "level_kognitif": "C2"
  }
]
JSON;
            case 'ISIAN_SINGKAT':
                return <<<JSON
[
  {
    "pertanyaan": "Teks soal isian singkat di sini...",
    "opsi_a": null,
    "opsi_b": null,
    "opsi_c": null,
    "opsi_d": null,
    "opsi_e": null,
    "kunci_jawaban": "Jawaban singkat",
    "pembahasan": "Penjelasan singkat mengenai jawaban...",
    "level_kognitif": "C1"
  }
]
JSON;
            default:
                return '[]';
        }
    }

    // ─────────────────────────────────────────────────────────
    //  API REQUEST (cURL)
    // ─────────────────────────────────────────────────────────

    /**
     * Kirim prompt ke Gemini API dan terima response.
     *
     * @param string $prompt Teks prompt lengkap
     * @return array|null Array hasil parsed JSON, atau null jika gagal
     */
    public function sendRequest(string $prompt): ?array {

        $this->lastError = null;
        $this->lastRawResponse = null;

        if (!$this->isConfigured()) {
            $this->lastError = 'API Key untuk model ini belum dikonfigurasi di Pengaturan AI.';
            return null;
        }

        if ($this->isGroqModel()) {
            return $this->sendGroqRequest($prompt);
        } else {
            return $this->sendGeminiRequest($prompt);
        }
    }

    private function sendGroqRequest(string $prompt): ?array {
        $url = "https://api.groq.com/openai/v1/chat/completions";
        $requestBody = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
            'response_format' => ['type' => 'json_object']
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($requestBody),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->groqKey
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            $this->lastError = "Gagal terhubung ke Groq API. cURL Error: {$curlError}";
            return null;
        }
        $this->lastRawResponse = $response;
        
        if ($httpCode !== 200) {
            $errorBody = json_decode($response, true);
            $errorMessage = $errorBody['error']['message'] ?? 'Unknown error';
            $this->lastError = "Groq API Error (HTTP {$httpCode}): {$errorMessage}";
            return null;
        }
        
        $data = json_decode($response, true);
        $content = $data['choices'][0]['message']['content'] ?? '';
        return $this->parseJsonResponseText($content);
    }

    private function sendGeminiRequest(string $prompt): ?array {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->geminiKey}";

        $requestBody = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature'     => $this->temperature,
                'maxOutputTokens' => $this->maxTokens,
                'responseMimeType' => 'application/json',
            ]
        ];

        $jsonPayload = json_encode($requestBody);

        // ── CURL REQUEST ──
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonPayload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => false, // XAMPP lokal
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // ── Handle cURL error ──
        if ($response === false) {
            $this->lastError = "Gagal terhubung ke Gemini API. cURL Error: {$curlError}";
            return null;
        }

        $this->lastRawResponse = $response;

        // ── Handle HTTP error ──
        if ($httpCode !== 200) {
            $errorBody = json_decode($response, true);
            $errorMessage = 'Unknown error';
            if (isset($errorBody['error']['message'])) {
                $errorMessage = $errorBody['error']['message'];
            }
            $this->lastError = "Gemini API Error (HTTP {$httpCode}): {$errorMessage}";
            return null;
        }
        
        $data = json_decode($response, true);
        $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        
        if (empty($content)) {
            $blockReason = $data['candidates'][0]['finishReason'] ?? 'UNKNOWN';
            if ($blockReason === 'SAFETY') {
                $this->lastError = 'Konten diblokir oleh Safety Filter. Coba gunakan materi berbeda.';
            } else {
                $this->lastError = 'Response AI kosong. Finish reason: ' . $blockReason;
            }
            return null;
        }
        
        return $this->parseJsonResponseText($content);
    }


    // ─────────────────────────────────────────────────────────
    //  RESPONSE PARSER
    // ─────────────────────────────────────────────────────────

    /**
     * Parse raw response dari Gemini API menjadi array soal.
     *
     * @param string $rawResponse Raw JSON response body
     * @return array|null Array soal yang sudah tervalidasi, atau null jika gagal
     */
    private function parseJsonResponseText(string $text): ?array {
        $text = $this->cleanJsonText($text);

        // Parse JSON soal
        $soalArray = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->lastError = 'JSON soal dari AI tidak valid: ' . json_last_error_msg() . '. Raw text: ' . substr($text, 0, 300);
            return null;
        }

        if (!is_array($soalArray) || empty($soalArray)) {
            $this->lastError = 'Response AI kosong atau bukan format array.';
            return null;
        }

        // Validasi dan normalisasi setiap item soal
        $validated = [];
        foreach ($soalArray as $idx => $soal) {
            $item = $this->validateSoalItem($soal, $idx);
            if ($item !== null) {
                $validated[] = $item;
            }
        }

        if (empty($validated)) {
            $this->lastError = 'Tidak ada soal yang valid dari response AI.';
            return null;
        }

        return $validated;
    }

    /**
     * Bersihkan teks JSON dari kemungkinan wrapper markdown
     */
    private function cleanJsonText(string $text): string {
        $text = trim($text);

        // Hapus ```json ... ``` wrapper jika ada
        if (preg_match('/^```(?:json)?\s*\n?(.*?)\n?\s*```$/s', $text, $matches)) {
            $text = $matches[1];
        }

        // Hapus karakter BOM jika ada
        $text = preg_replace('/^\xEF\xBB\xBF/', '', $text);

        return trim($text);
    }

    /**
     * Validasi dan normalisasi satu item soal
     *
     * @param mixed $soal Data soal dari JSON
     * @param int $idx Index soal untuk logging
     * @return array|null Item soal yang tervalidasi, null jika tidak valid
     */
    private function validateSoalItem($soal, int $idx): ?array {
        if (!is_array($soal)) {
            return null;
        }

        // Field wajib
        if (empty($soal['pertanyaan'])) {
            return null;
        }

        // Validasi level_kognitif
        $validLevels = ['C1', 'C2', 'C3', 'C4', 'C5', 'C6'];
        $level = strtoupper(trim($soal['level_kognitif'] ?? 'C3'));
        if (!in_array($level, $validLevels)) {
            $level = 'C3'; // fallback
        }

        return [
            'pertanyaan'    => trim($soal['pertanyaan']),
            'opsi_a'        => isset($soal['opsi_a']) ? trim($soal['opsi_a']) : null,
            'opsi_b'        => isset($soal['opsi_b']) ? trim($soal['opsi_b']) : null,
            'opsi_c'        => isset($soal['opsi_c']) ? trim($soal['opsi_c']) : null,
            'opsi_d'        => isset($soal['opsi_d']) ? trim($soal['opsi_d']) : null,
            'opsi_e'        => isset($soal['opsi_e']) ? trim($soal['opsi_e']) : null,
            'kunci_jawaban' => trim($soal['kunci_jawaban'] ?? ''),
            'pembahasan'    => trim($soal['pembahasan'] ?? ''),
            'level_kognitif' => $level,
        ];
    }

    // ─────────────────────────────────────────────────────────
    //  HIGH-LEVEL API: GENERATE SOAL
    // ─────────────────────────────────────────────────────────

    /**
     * Method utama: Generate soal berdasarkan parameter input.
     * Menggabungkan buildPrompt + sendRequest dalam satu panggilan.
     *
     * @param array $params Parameter dari form:
     *   - mapel_nama (string)
     *   - kelas_nama (string)
     *   - materi (string)
     *   - tipe_soal (string: PG|ESSAY|BENAR_SALAH|ISIAN_SINGKAT)
     *   - tingkat_kesulitan (string: Mudah|Sedang|Sulit|Campuran)
     *   - level_bloom (string: "C1, C2" | "C3, C4" | "C5, C6")
     *   - jumlah_soal (int: 1-20)
     *
     * @return array ['success' => bool, 'data' => array|null, 'error' => string|null, 'prompt' => string, 'raw_response' => string|null]
     */
    public function generateSoal(array $params): array {

        // Build prompt
        $prompt = $this->buildPrompt($params);

        // Kirim ke API
        $result = $this->sendRequest($prompt);

        return [
            'success'      => $result !== null,
            'data'         => $result,
            'error'        => $this->lastError,
            'prompt'       => $prompt,
            'raw_response' => $this->lastRawResponse,
        ];
    }
}

@extends('layouts.chat')

@section('title', 'Dokumentasi API - JriGPT')

@section('sidebar')
@include('public.partials.sidebar')
@endsection

@section('content')
<section class="flex-1 overflow-y-auto bg-[#0a0f16] text-gray-300 selection:bg-emerald-500/30">
    <div class="max-w-4xl mx-auto px-6 py-12">
        <header class="mb-12">
            <h1 class="text-4xl font-bold text-white mb-4 tracking-tight">Dokumentasi JriGPT API</h1>
            <p class="text-lg text-gray-400">Panduan lengkap untuk mengintegrasikan JriGPT-1 ke dalam aplikasi Anda. API
                ini menggunakan standar REST dengan format JSON.</p>
        </header>

        <div class="space-y-16">
            <!-- Base URL -->
            <section id="base-url">
                <h2 class="text-2xl font-semibold text-white mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                    Base URL
                </h2>
                <div class="bg-black/40 border border-white/10 rounded-xl p-4 font-mono text-emerald-400">
                    https://api.jrigpt.fajriag.my.id/api/v1
                </div>
            </section>

            <!-- Autentikasi -->
            <section id="authentication">
                <h2 class="text-2xl font-semibold text-white mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                    Autentikasi
                </h2>
                <p class="mb-4 text-gray-400">Sertakan API key Anda di header Authorization pada setiap permintaan. API
                    key dibuat dari Dashboard Anda.</p>
                <div class="bg-black/40 border border-white/10 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-2 uppercase tracking-wider font-bold">Format Header</p>
                    <code class="text-emerald-400">Authorization: Bearer jrigpt-xxxxxxxxxxxxxxxx</code>
                </div>
            </section>

            <!-- Chat Completions -->
            <section id="endpoint">
                <div class="flex items-center gap-3 mb-6">
                    <span
                        class="px-3 py-1 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 rounded text-sm font-bold">POST</span>
                    <h2 class="text-2xl font-semibold text-white">/chat/completions</h2>
                </div>

                <h3 class="text-xl font-medium text-white mb-4">Chat Completions</h3>
                <p class="mb-6">Membuat respons model untuk percakapan yang diberikan. Ini adalah endpoint utama untuk
                    semua interaksi teks dengan JriGPT-1.</p>

                <div class="grid md:grid-cols-2 gap-4 mb-8">
                    <div class="bg-blue-500/5 border border-blue-500/20 rounded-xl p-5">
                        <h4 class="text-blue-400 font-bold text-sm mb-2 uppercase">Kapasitas Ekstra Besar</h4>
                        <p class="text-sm">Endpoint ini mendukung hingga 163.840 token atau setara ~500.000 karakter
                            dalam satu request.</p>
                    </div>
                    <div class="bg-purple-500/5 border border-purple-500/20 rounded-xl p-5">
                        <h4 class="text-purple-400 font-bold text-sm mb-2 uppercase">Model ID</h4>
                        <p class="text-sm">Gunakan model ID <code class="text-purple-300">jrigpt</code> untuk mengakses
                            model utama JriGPT.</p>
                    </div>
                </div>

                <h4 class="text-white font-bold mb-4 uppercase text-xs tracking-widest">Parameter Request</h4>
                <div class="overflow-x-auto rounded-xl border border-white/10">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white/5 text-gray-400">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Parameter</th>
                                <th class="px-4 py-3 font-semibold">Tipe</th>
                                <th class="px-4 py-3 font-semibold">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr>
                                <td class="px-4 py-4 text-emerald-400">model <span
                                        class="text-[10px] text-red-400 font-bold uppercase ml-1">Wajib</span></td>
                                <td class="px-4 py-4 italic text-gray-500">string</td>
                                <td class="px-4 py-4">ID model. Gunakan <code
                                        class="bg-white/5 px-1 rounded text-white">jrigpt</code>.</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-4 text-emerald-400">messages <span
                                        class="text-[10px] text-red-400 font-bold uppercase ml-1">Wajib</span></td>
                                <td class="px-4 py-4 italic text-gray-500">array</td>
                                <td class="px-4 py-4">Daftar pesan. Setiap item memiliki <code
                                        class="text-gray-300">role</code> (system, user, assistant) dan <code
                                        class="text-gray-300">content</code>.</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-4 text-emerald-400">stream</td>
                                <td class="px-4 py-4 italic text-gray-500">boolean</td>
                                <td class="px-4 py-4">Jika true, respons dikirim bertahap via SSE. Default: false.</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-4 text-emerald-400">temperature</td>
                                <td class="px-4 py-4 italic text-gray-500">number</td>
                                <td class="px-4 py-4">Kontrol keacakan output. Antara 0–2. Default: 1.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Examples -->
            <section id="examples">
                <h2 class="text-2xl font-semibold text-white mb-6">Contoh Kode</h2>
                <div x-data="{ lang: 'curl' }" class="border border-white/10 rounded-2xl overflow-hidden bg-[#0c1117]">
                    <div class="flex border-b border-white/10 bg-white/5">
                        <button @click="lang = 'curl'"
                            :class="lang === 'curl' ? 'bg-[#0c1117] border-t-2 border-emerald-500 text-white' : 'text-gray-500'"
                            class="px-6 py-3 font-medium transition">cURL</button>
                        <button @click="lang = 'python'"
                            :class="lang === 'python' ? 'bg-[#0c1117] border-t-2 border-emerald-500 text-white' : 'text-gray-500'"
                            class="px-6 py-3 font-medium transition">Python</button>
                        <button @click="lang = 'php'"
                            :class="lang === 'php' ? 'bg-[#0c1117] border-t-2 border-emerald-500 text-white' : 'text-gray-500'"
                            class="px-6 py-3 font-medium transition">PHP</button>
                    </div>
                    <div class="p-6">
                        <pre x-show="lang === 'curl'" class="text-sm text-gray-300 font-mono overflow-x-auto">curl https://api.jrigpt.fajriag.my.id/api/v1/chat/completions \
  -H "Authorization: Bearer jrigpt-..." \
  -H "Content-Type: application/json" \
  -d '{
    "model": "jrigpt",
    "messages": [
      {"role": "system", "content": "Kamu adalah asisten yang membantu."},
      {"role": "user",   "content": "Jelaskan apa itu REST API."}
    ]
  }'</pre>
                        <pre x-show="lang === 'python'" class="text-sm text-gray-300 font-mono overflow-x-auto">import requests

resp = requests.post(
    "https://api.jrigpt.fajriag.my.id/api/v1/chat/completions",
    headers={
        "Authorization": "Bearer jrigpt-...",
        "Content-Type": "application/json"
    },
    json={
        "model": "jrigpt",
        "messages": [
            {"role": "user", "content": "Jelaskan apa itu REST API."}
        ]
    }
)
print(resp.json()["choices"][0]["message"]["content"])</pre>
                        <pre x-show="lang === 'php'" class="text-sm text-gray-300 font-mono overflow-x-auto">$ch = curl_init('https://api.jrigpt.fajriag.my.id/api/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer jrigpt-...',
        'Content-Type: application/json',
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'model'    => 'jrigpt',
        'messages' => [['role' => 'user', 'content' => 'Jelaskan apa itu REST API.']],
    ]),
]);
$res = json_decode(curl_exec($ch), true);
echo $res['choices'][0]['message']['content'];</pre>
                    </div>
                </div>
            </section>

            <!-- Errors -->
            <section id="errors">
                <h2 class="text-2xl font-semibold text-white mb-6">Kode Error</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-xl">
                        <div class="text-emerald-400 font-bold mb-1">200</div>
                        <div class="text-xs text-gray-500 uppercase font-bold">Berhasil</div>
                    </div>
                    <div class="p-4 bg-orange-500/5 border border-orange-500/10 rounded-xl">
                        <div class="text-orange-400 font-bold mb-1">402</div>
                        <div class="text-xs text-gray-500 uppercase font-bold">Saldo Kurang</div>
                    </div>
                    <div class="p-4 bg-red-500/5 border border-red-500/10 rounded-xl">
                        <div class="text-red-400 font-bold mb-1">429</div>
                        <div class="text-xs text-gray-500 uppercase font-bold">Rate Limit</div>
                    </div>
                    <div class="p-4 bg-gray-500/5 border border-gray-500/10 rounded-xl">
                        <div class="text-gray-400 font-bold mb-1">500</div>
                        <div class="text-xs text-gray-500 uppercase font-bold">Server Error</div>
                    </div>
                </div>
            </section>
            <!-- CTA Section -->
            <section class="mt-20 p-8 rounded-3xl bg-gradient-to-br from-emerald-600 to-teal-700 text-center shadow-2xl shadow-emerald-900/20">
                <h2 class="text-3xl font-bold text-white mb-4">Siap untuk Memulai?</h2>
                <p class="text-emerald-100 mb-8 max-w-lg mx-auto text-lg text-balance">Dapatkan API Key Anda sekarang dan mulai bangun aplikasi cerdas dengan tenaga JriGPT-1.</p>
                <a href="https://api.jrigpt.fajriag.my.id/" target="_blank" class="inline-flex items-center gap-2 bg-white text-emerald-700 px-8 py-4 rounded-2xl font-bold text-lg hover:bg-emerald-50 transition-all hover:scale-105 shadow-xl">
                    Kunjungi JriGPT Cloud
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                        <polyline points="15 3 21 3 21 9" />
                        <line x1="10" y1="14" x2="21" y2="3" />
                    </svg>
                </a>
            </section>
        </div>

        <footer class="mt-20 pt-8 border-t border-white/10 text-center text-sm text-gray-400">
            &copy; {{ date('Y') }} JriGPT API Docs. Dibuat oleh Fajri Abdurahman Ghurri.
        </footer>
    </div>
</section>
@endsection
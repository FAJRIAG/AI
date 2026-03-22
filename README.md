# 🚀 JriGPT - Advanced AI Assistant with Web-Agent & Emotional Intelligence

<p align="center">
  <img src="https://cdn-icons-png.flaticon.com/512/2103/2103633.png" width="120" alt="JriGPT Logo">
</p>

JriGPT adalah asisten AI cerdas tingkat lanjut yang dikembangkan secara khusus oleh **Fajri Abdurahman Ghurri**. Platform ini dibangun menggunakan Laravel 12 dan ditenagai oleh model bahasa besar terbaru untuk memberikan pengalaman asisten yang personal, responsif, dan kaya fitur.

## ✨ Fitur Unggulan

### 🌐 Web-Agent (Browser Control) - **Powered by Jina Reader**
JriGPT tidak hanya memberikan ringkasan pencarian yang terbatas. Ia bisa bener-bener "mengunjungi" website spesifik (seperti iBox, Shopee, atau portal berita) menggunakan integrasi **Jina Reader**.
- **Deep Extraction**: Membaca isi lengkap halaman dalam format Markdown yang bersih.
- **Bypass Bot**: Mampu menembus proteksi JavaScript dan bot di situs-situs modern.
- **Agentic Loops**: Mampu melakukan rantai tindakan secara otomatis.

### 🎭 Emotional Tone Sync (Real-time Sentiment)
JriGPT memahami perasaan Anda. Antarmuka (UI) akan berubah warna secara dinamis berdasarkan cara Anda mengetik:
- 🔴 **Panic Mode**: UI berubah merah saat mendesak.
- 🟣 **Creative Mode**: UI berubah ungu saat brainstorming.
- 🔵 **Thoughtful Mode**: UI berubah biru untuk analisis mendalam.

### 🚀 Premium Artifacts Studio 2.0
Tampilan eksklusif layaknya asisten koding profesional (**Claude-Style**).
- **Live Preview**: Render HTML/CSS/JS dan diagram **Mermaid** secara instan.
- **Resizable Sidebar**: Panel preview yang fleksibel dengan fitur *glassmorphism* tingkat lanjut.
- **Smart Toggle**: Kontrol penuh untuk membuka/menutup preview dengan transisi ultra-smooth.

### 📱 Universal Responsiveness (Cross-Device)
JriGPT didesain untuk pengalaman premium di perangkat apapun:
- **Mobile (HP)**: Antarmuka yang dioptimalkan untuk layar kecil dengan input bar yang cerdas.
- **Ultra-wide Monitor**: Pembatasan lebar Artifact (Max 800px) untuk menjaga keterbacaan di layar raksasa.

### 📁 Project Workspaces (Konteks Proyek) - **VIP Exclusive**
Kelola proyek koding Anda secara terpisah dengan sistem Workspace yang cerdas.
- **Multi-Workspace**: Buat, ganti, dan hapus workspace khusus untuk proyek yang berbeda (misal: "Web AI", "Personal", "Koding").
- **Project Memory**: Berikan instruksi khusus per-workspace (seperti tech stack, aturan koding, atau tujuan proyek).
- **Auto-Context**: JriGPT akan otomatis mengingat semua konteks di dalam workspace tersebut tanpa perlu diingatkan kembali di setiap chat baru.
- **Smart Filtering**: Sidebar yang bersih, hanya menampilkan histori chat yang relevan dengan workspace yang sedang aktif.

### 🧠 Intelligent Memory & Multimodal
- **Memory**: Mengingat preferensi teknis dan fakta penting Anda secara otomatis (User-level & Project-level).
- **Vision**: Analisis gambar dan dokumen (**PDF, TXT, CSV**) secara mendalam.

## 🛠️ Stack Teknologi
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade, Vanilla JS, Tailwind CSS, CSS Variables for Dynamic Themes
- **AI Engine**: Groq / OpenAI / JriGPT Private API
- **Web Reader**: Jina Reader API Integration
- **Tools**: Marked.js, KaTeX, Mermaid.js, Highlight.js

## 🚀 Instalasi Cepat

1. **Clone Repo**
   ```bash
   git clone https://github.com/FAJRIAG/AI.git
   cd AI
   ```

2. **Setup Env**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Install Dependencies**
   ```bash
   composer install
   npm install
   npm run build
   ```

4. **Run Server**
   ```bash
   php artisan serve
   ```

---
## 👨‍💻 Dikembangkan Oleh
**Fajri Abdurahman Ghurri**  
Asisten AI ini terus dikembangkan untuk menjadi pendamping koding dan produktivitas terbaik Anda.

---
© 2026 JriGPT. All Rights Reserved.

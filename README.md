# 🚀 JriGPT - Advanced AI Assistant with Web-Agent & Emotional Intelligence

<p align="center">
  <img src="https://cdn-icons-png.flaticon.com/512/2103/2103633.png" width="120" alt="JriGPT Logo">
</p>

JriGPT adalah asisten AI cerdas tingkat lanjut yang dikembangkan secara khusus oleh **Fajri Abdurahman Ghurri**. Platform ini dibangun menggunakan Laravel 12 dan ditenagai oleh model bahasa besar terbaru untuk memberikan pengalaman asisten yang personal, responsif, dan kaya fitur.

## ✨ Fitur Unggulan

### 🌐 Web-Agent (Browser Control) - **Powered by Jina Reader**
JriGPT tidak hanya memberikan ringkasan pencarian yang terbatas. Ia bisa bener-bener "mengunjungi" website spesifik (seperti iBox, Shopee, atau portal berita) menggunakan integrasi **Jina Reader**.
- **Deep Extraction**: Membaca isi lengkap halaman dalam format Markdown yang bersih.
- **Bypass Bot**: Mampu menembus proteksi JavaScript dan bot di situs-situs modern (seperti iBox).
- **Agentic Loops**: Mampu melakukan rantai tindakan (Cari -> Pilih Link -> Baca Detail) secara otomatis.

### 🎭 Emotional Tone Sync (Real-time Sentiment)
JriGPT memahami perasaan Anda. Antarmuka (UI) JriGPT akan berubah warna secara dinamis, dan nada bicara AI akan menyesuaikan diri berdasarkan cara Anda mengetik:
- 🔴 **Panic Mode**: UI berubah merah saat Anda mengetik dengan CAPSLOCK atau kata-kata mendesak. JriGPT akan merespons dengan lebih tenang.
- 🟣 **Creative Mode**: UI berubah ungu saat brainstorming. AI akan menjadi lebih imajinatif.
- 🔵 **Thoughtful Mode**: UI berubah biru langit untuk analisis mendalam.
- 🟢 **Calm Mode**: UI hijau emerald standar yang elegan.

### 🧠 Intelligent Memory
JriGPT mampu mengekstrak dan mengingat fakta penting tentang Anda (nama, hobi, preferensi teknis) secara otomatis untuk memberikan jawaban yang dipersonalisasi di masa depan.

### 🚀 Artifacts UI
Tampilan layaknya asisten profesional yang dapat merender kode HTML/CSS/JS dan diagram **Mermaid** secara real-time di panel samping.

### 👁️ Multimodal Vision & Document Analysis
- **Vision**: Analisis gambar secara mendalam.
- **Docs**: Upload file **PDF, TXT,** atau **CSV** untuk rangkuman otomatis.

## 🛠️ Stack Teknologi
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade, Vanilla JS, Tailwind CSS, CSS Variables for Themes
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

# JriGPT - Premium AI Assistant

<p align="center">
  <img src="https://cdn-icons-png.flaticon.com/512/2103/2103633.png" width="120" alt="JriGPT Logo">
</p>

JriGPT adalah asisten AI cerdas tingkat lanjut yang dikembangkan secara khusus oleh **Fajri Abdurahman Ghurri**. Platform ini dibangun menggunakan Laravel 12 dan ditenagai oleh model bahasa besar terbaru untuk memberikan pengalaman asisten yang personal, responsif, dan kaya fitur.

## ✨ Fitur Unggulan

### 🌈 Emotional Tone Sync (Terbaru!)
JriGPT sekarang memiliki "perasaan". UI dan gaya bicara AI akan berubah secara otomatis berdasarkan mood pengguna:
- **Calm (Emerald)**: Suasana santai dan ramah.
- **Panic (Rose/Red)**: Terdeteksi saat ketikan panik/kapital. AI merespons dengan tenang dan solutif.
- **Creative (Violet)**: Mode brainstorming dengan ide-ide imajinatif.
- **Thoughtful (Sky Blue)**: Mode analisis mendalam untuk pertanyaan filosofis atau teknis.

### 🧠 Intelligent Memory
JriGPT mampu mengekstrak dan mengingat fakta penting tentang Anda (nama, hobi, preferensi teknis) secara otomatis untuk memberikan jawaban yang dipersonalisasi di masa depan.

### 👁️ Multimodal Vision
Kirimkan gambar, dan JriGPT dapat melihat, menganalisis, serta mendeskripsikan isi gambar tersebut dengan detail.

### 📄 Document Analysis
Upload file **PDF, TXT,** atau **CSV**. JriGPT akan membaca isinya dan membantu Anda merangkum atau menjawab pertanyaan berdasarkan dokumen tersebut.

### 🚀 Artifacts UI
Tampilan layaknya asisten profesional yang dapat merender kode HTML/CSS/JS dan diagram **Mermaid** secara real-time di panel samping.

### 🌐 Real-time Web Search
Terhubung ke internet untuk mencari informasi terbaru, harga saham, cuaca, atau berita terkini yang tidak ada dalam data pelatihan model.

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade, Vanilla JS, Tailwind CSS
- **AI Engine**: Groq / OpenAI API via Custom Service
- **Tools**: Marked.js (Markdown), KaTeX (Math), Mermaid.js (Diagrams), Highlight.js (Syntax Highlighting)

---

## 🚀 Instalasi Cepat

1. **Clone repositori**:
   ```bash
   git clone https://github.com/FAJRIAG/AI.git
   cd AI
   ```

2. **Setup Environment**:
   ```bash
   cp .env.example .env
   # Update AI_API_KEY dan konfigurasi lainnya di .env
   ```

3. **Install Dependensi & Jalankan**:
   ```bash
   composer install
   npm install
   php artisan key:generate
   php artisan migrate
   npm run build
   ```

4. **Jalankan Aplikasi**:
   ```bash
   php artisan serve
   # Buka http://localhost:8000
   ```

---

## 👨‍💻 Dikembangkan Oleh
**Fajri Abdurahman Ghurri**  
Asisten AI ini terus dikembangkan untuk menjadi pendamping koding dan produktivitas terbaik Anda.

---
© 2026 JriGPT. All Rights Reserved.

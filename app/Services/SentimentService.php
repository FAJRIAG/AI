<?php

namespace App\Services;

class SentimentService
{
    /**
     * Get tone instructions for the AI based on the detected mood.
     */
    public static function getToneInstruction(string $mood): string
    {
        return match ($mood) {
            'panic' => "PENTING: Pengguna sedang panik atau dalam situasi darurat. Jawablah dengan nada yang sangat tenang, empati, langsung ke solusi, dan hindari basa-basi atau bercanda.",
            'creative' => "PENTING: Pengguna sedang dalam mode kreatif. Jawablah dengan nada yang antusias, imajinatif, berikan banyak ide tambahan, dan gunakan bahasa yang menginspirasi.",
            'thoughtful' => "PENTING: Pengguna sedang berpikir mendalam atau analitis. Jawablah dengan nada yang logis, terstruktur, berikan penjelasan mendalam (fakta/data), dan gunakan bahasa yang intelektual.",
            default => "PENTING: Pengguna sedang santai. Jawablah dengan ramah, boleh menyelipkan sedikit humor yang sopan, dan jadilah asisten yang asyik diajak mengobrol.",
        };
    }
}

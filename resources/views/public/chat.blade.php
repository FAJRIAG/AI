@extends('layouts.chat')
@section('title', 'JriGPT')

@section('content')
  <section id="scrollArea" class="flex-1 overflow-y-auto" data-page="public-chat" data-sid="{{ $sid }}">
    <div id="chatList" class="max-w-3xl mx-auto px-4 py-6 space-y-6">
      @forelse(($history ?? []) as $m)
        @if(($m['role'] ?? '') === 'assistant')
          <div class="fade-in flex gap-3">
            <div class="shrink-0 mt-1 size-8 rounded-full bg-[#1f2937] grid place-items-center text-xs">JG</div>
            <div class="ai-raw-content hidden">{{ $m['content'] ?? '' }}</div>
            <article class="ai-prose prose prose-invert max-w-none flex-1 min-w-0"></article>
          </div>
        @else
          <div class="fade-in flex">
            <div class="ml-auto max-w-[80%] rounded-2xl bg-[#1a1f2a] px-4 py-2 ring-1 ring-white/10">
              <div class="whitespace-pre-wrap leading-6 text-gray-100">{{ $m['content'] ?? '' }}</div>
            </div>
          </div>
        @endif
      @empty
        <div class="text-center text-gray-400 mt-60">
          <p class="font-medium">Hari ini ada agenda apa?</p>
          <p class="text-sm text-gray-500">Ketik pertanyaan di bawah, tekan Enter untuk kirim.</p>
        </div>
      @endforelse
    </div>

    {{-- Typing --}}
    <div id="typing" class="max-w-3xl mx-auto px-4 pb-4 hidden">
      <div class="flex gap-3">
        <div class="shrink-0 mt-1 size-8 rounded-full bg-[#1f2937] grid place-items-center text-xs">AI</div>
        <div class="rounded-2xl bg-white/5 px-3 py-2 text-gray-300 ring-1 ring-white/10">
          <span class="typing"><span></span><span></span><span></span></span>
        </div>
      </div>
    </div>

    {{-- Scroll to bottom --}}
    <button id="toBottom"
      class="hidden fixed bottom-24 right-6 md:right-10 rounded-full bg-white/10 hover:bg-white/20 border border-white/10 px-3 py-2 text-sm backdrop-blur">
      ↓ Scroll to bottom
    </button>
  </section>

  {{-- Composer --}}
  <footer class="border-t border-white/10 bg-gradient-to-b from-transparent">
    <div class="max-w-3xl mx-auto px-4 py-4">
      <div class="flex items-end gap-2">
        <textarea id="prompt" rows="1" placeholder="Tulis pesan…" class="flex-1 resize-none rounded-2xl bg-[#0c1117] border border-white/10 focus:outline-none focus:ring-2 focus:ring-emerald-600/60
                     px-4 py-3 leading-6 text-gray-100 placeholder:text-gray-500"></textarea>
        <button id="send"
          class="shrink-0 rounded-2xl bg-emerald-600 hover:bg-emerald-500 px-4 py-3 font-semibold transition">Kirim</button>
        <button id="stop" class="shrink-0 hidden rounded-2xl bg-white/10 px-4 py-3">Stop</button>
      </div>
      <div class="mt-2 flex items-center justify-between text-[11px] text-gray-400">
        <div>Enter = kirim • Shift+Enter = baris baru • Markdown & code didukung</div>
        {{-- <button id="regen"
          class="px-2 py-1 rounded bg-white/5 border border-white/10 hover:bg-white/10">Regenerate</button> --}}
      </div>
    </div>
  </footer>
@endsection

@push('modals')
  @include('public.partials.modals.rename')
@endpush
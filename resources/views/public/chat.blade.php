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
              @if(!empty($m['attachment_url']))
                @php
                  $isImg = preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $m['attachment_url']);
                @endphp
                @if($isImg)
                  <img src="{{ \Storage::url($m['attachment_url']) }}" class="max-w-xs mb-2 rounded border border-white/10">
                @else
                  <div class="flex items-center gap-2 mb-2 p-2 bg-white/5 rounded border border-white/10">
                      <img src="https://cdn-icons-png.flaticon.com/512/2991/2991108.png" class="size-6 object-contain">
                      <span class="text-xs text-gray-400 truncate max-w-[150px]">{{ basename($m['attachment_url']) }}</span>
                  </div>
                @endif
              @endif
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
      class="hidden fixed bottom-24 right-6 md:right-10 z-50 rounded-full bg-black/60 hover:bg-emerald-600/20 border border-white/20 hover:border-emerald-500/50 px-4 py-2 text-sm backdrop-blur-md text-white transition-all shadow-xl">
      ↓ Scroll to bottom
    </button>
  </section>

  {{-- Composer dipanggil dari partials --}}
  @include('public.partials.composer')
@endsection

@push('modals')
  @include('public.partials.modals.rename')
@endpush
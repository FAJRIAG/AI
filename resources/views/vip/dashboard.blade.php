@extends('layouts.chat')
@section('title','VIP Chat')

@section('sidebar')
  @include('vip.partials.sidebar', [
    'projects' => $projects ?? collect(),
    'sessions' => $sessions ?? null, // optional flat list
  ])
@endsection

@section('content')
  <section id="scrollArea" class="flex-1 overflow-y-auto" data-page="vip-chat"
           data-session-id="{{ $currentSession->id ?? '' }}">
    <div id="chatList" class="max-w-3xl mx-auto px-4 py-6 space-y-6">
      @if(isset($currentSession) && $currentSession->messages && $currentSession->messages->count())
        @foreach($currentSession->messages as $m)
          @php $role = $m->role ?? 'user'; $content = $m->content ?? ''; @endphp
          @if($role === 'assistant')
            <div class="fade-in flex gap-3">
              <div class="shrink-0 mt-1 size-8 rounded-full bg-[#1f2937] grid place-items-center text-xs">JG</div>
              {{-- raw content parsed by frontend on load --}}
              <div class="ai-raw-content hidden">{{ $content }}</div>
              <article class="ai-prose prose prose-sm prose-invert max-w-none flex-1 min-w-0"></article>
            </div>
          @else
            <div class="fade-in flex">
              <div class="ml-auto max-w-[80%] rounded-2xl bg-[#1a1f2a] px-4 py-2 ring-1 ring-white/10">
                <div class="whitespace-pre-wrap leading-6 text-gray-100">{{ $content }}</div>
              </div>
            </div>
          @endif
        @endforeach
      @else
        <div class="text-center text-gray-400 mt-60">
          <p class="font-medium">Selamat datang, {{ auth()->user()->name }} </p>
          <p class="text-sm text-gray-500">Ketik pertanyaan di bawah, tekan Enter untuk kirim.</p>
        </div>
      @endif
    </div>

    {{-- Typing --}}
    <div id="typing" class="max-w-3xl mx-auto px-4 pb-4 hidden">
      <div class="flex gap-3">
        <div class="shrink-0 mt-1 size-8 rounded-full bg-[#1f2937] grid place-items-center text-xs">JG</div>
        <div class="rounded-2xl bg-white/5 px-3 py-2 text-gray-300 ring-1 ring-white/10">
          <span class="typing"><span></span><span></span><span></span></span>
        </div>
      </div>
    </div>

    <button id="toBottom" class="hidden fixed bottom-24 right-6 md:right-10 rounded-full bg-white/10 hover:bg-white/20 border border-white/10 px-3 py-2 text-sm backdrop-blur">
      ↓ Scroll to bottom
    </button>
  </section>

  {{-- Composer dipanggil di sini agar tidak dobel --}}
  @include('public.partials.composer')
@endsection

@push('modals')
  {{-- Modal rename (dipakai sidebar VIP) --}}
  @include('public.partials.modals.rename')
@endpush

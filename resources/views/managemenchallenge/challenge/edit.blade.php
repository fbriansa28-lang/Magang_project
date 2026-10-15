<x-app-layout>
    <x-slot name="header"><h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">KELOLA SOAL</h2></x-slot>

    @can('edit challenge')
    <div class="mx-auto mt-5 w-full max-w-[1200px] min-w-0 pb-12 font-mono">

        @include('managemenchallenge.challenge.partials.stat-cards')

        <form method="POST" action="{{ route('challenges.update', $challenge) }}">
            @csrf
            @method('PUT')
            @include('managemenchallenge.challenge.partials.form', ['challenge' => $challenge])
        </form>

    </div>
    @endcan
</x-app-layout>

<x-app-layout>
    <x-slot name="header"><div class="text-[15px] font-semibold tracking-wide text-gray-200 light:text-[#1a2e28]">TAMBAH MATERI — KELOLA BELAJAR</div></x-slot>
    @can('create kelola learning')
    <div class="mx-auto max-w-[1180px] pb-12">@if($errors->any())<div class="mb-4 border border-red-500/25 bg-red-500/10 px-3.5 py-2.5 text-[12px] text-red-400 light:bg-red-50 light:text-red-700">@foreach($errors->all() as $error)<span class="block">{{ $error }}</span>@endforeach</div>@endif<form method="POST" action="{{ route('learning-materials.store') }}" enctype="multipart/form-data">@csrf @include('managemenlearning.partials.form')<input type="hidden" name="is_active" value="1"></form></div>
    @endcan
</x-app-layout>
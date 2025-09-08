@extends('layouts.app')

@section('content')
{{-- Font Awesome (icons) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<div class="max-w-2xl mx-auto">
  <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center gap-2">
    <i class="fa-solid fa-pen-to-square text-purple-600"></i>
    Edit Category
  </h1>

  <form action="{{ route('admin.categories.update',$category) }}" method="POST"
        class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 space-y-5">
    @csrf
    @method('PUT')

    {{-- Name --}}
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
        <i class="fa-solid fa-tag text-purple-500 mr-1"></i> Name
      </label>
      <input id="name" name="name" type="text"
             value="{{ old('name',$category->name) }}"
             class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3 py-2 text-gray-800"
             required>
      @error('name')
        <p class="text-pink-600 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Description --}}
    <div>
      <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
        <i class="fa-solid fa-align-left text-purple-500 mr-1"></i> Description
      </label>
      <textarea id="description" name="description" rows="3"
                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 px-3 py-2 text-gray-800">{{ old('description',$category->description) }}</textarea>
    </div>

    {{-- Actions --}}
    <div class="flex justify-end gap-3 pt-2">
      <a href="{{ route('admin.categories.index') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
        <i class="fa-solid fa-arrow-left"></i>
        Cancel
      </a>
      <button type="submit"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 text-white font-semibold shadow transition">
        <i class="fa-solid fa-save"></i>
        Update
      </button>
    </div>
  </form>
</div>
@endsection

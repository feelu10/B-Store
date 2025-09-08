
@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold text-purple-700 mb-4">New Category</h1>

<form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white rounded shadow p-4 grid gap-4 max-w-xl">
  @csrf
  <div>
    <label class="block text-sm text-gray-600 mb-1">Name</label>
    <input name="name" class="w-full border rounded px-3 py-2" required>
    @error('name') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
  </div>
  <div>
    <label class="block text-sm text-gray-600 mb-1">Description</label>
    <textarea name="description" rows="3" class="w-full border rounded px-3 py-2"></textarea>
  </div>
  <div class="flex gap-2">
    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded border">Cancel</a>
    <button class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded">Save</button>
  </div>
</form>
@endsection

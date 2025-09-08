@extends('layouts.app')

@section('content')
  <h1 class="text-2xl font-bold text-purple-700">Admin Dashboard</h1>
  <p class="mt-2">Welcome, manage products & categories.</p>
  <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <a href="{{ route('admin.products.index') }}" class="bg-white rounded shadow p-4 hover:shadow-lg transition">
      <div class="text-pink-600 font-bold">Products</div>
      <div class="text-sm text-gray-600">Create, edit & delete products</div>
    </a>
    <a href="{{ route('admin.categories.index') }}" class="bg-white rounded shadow p-4 hover:shadow-lg transition">
      <div class="text-pink-600 font-bold">Categories</div>
      <div class="text-sm text-gray-600">Manage product categories</div>
    </a>
  </div>
@endsection

@extends('layouts.app')

@section('content')
{{-- Font Awesome (icons) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<div class="flex items-center justify-between mb-6">
  <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Categories</h1>
  <a href="{{ route('admin.categories.create') }}"
     class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 text-white font-semibold px-4 py-2 rounded-lg shadow transition">
    <i class="fa-solid fa-plus"></i>
    New Category
  </a>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
  <table class="w-full text-sm">
    <thead class="bg-gradient-to-r from-purple-50 to-pink-50 text-gray-700">
      <tr>
        <th class="text-left p-4 font-semibold">Name</th>
        <th class="text-left p-4 font-semibold">Description</th>
        <th class="p-4 text-right font-semibold">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      @foreach($categories as $c)
        <tr class="hover:bg-gray-50 transition">
          <td class="p-4 font-medium text-gray-800">{{ $c->name }}</td>
          <td class="p-4 text-gray-600">{{ $c->description }}</td>
          <td class="p-4">
            <div class="flex items-center justify-end gap-2">
              {{-- Edit --}}
              <a href="{{ route('admin.categories.edit',$c) }}"
                 class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-purple-200 text-purple-700 hover:bg-purple-50 hover:border-purple-300 transition"
                 title="Edit">
                <i class="fa-solid fa-pen-to-square"></i>
                <span class="hidden sm:inline font-medium">Edit</span>
              </a>

              {{-- Delete (SweetAlert) --}}
              <form action="{{ route('admin.categories.destroy',$c) }}" method="POST" class="inline js-delete-form">
                @csrf @method('DELETE')
                <button type="button"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-pink-200 text-pink-700 hover:bg-pink-50 hover:border-pink-300 transition js-delete-btn"
                        data-category-name="{{ $c->name }}"
                        title="Delete">
                  <i class="fa-solid fa-trash-can"></i>
                  <span class="hidden sm:inline font-medium">Delete</span>
                </button>
              </form>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-6">
  {{ $categories->links('pagination::tailwind') }}
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-delete-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const form = btn.closest('.js-delete-form');
        const name = btn.getAttribute('data-category-name') || 'this category';

        Swal.fire({
          title: 'Delete category?',
          html: `<div class="text-gray-700">You are about to delete <b>${name}</b>.<br>This action cannot be undone.</div>`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete',
          cancelButtonText: 'Cancel',
          reverseButtons: true,
          buttonsStyling: false,
          customClass: {
            confirmButton: 'swal2-confirm inline-flex items-center justify-center px-4 py-2 rounded-lg bg-pink-600 hover:bg-pink-700 text-white font-semibold',
            cancelButton: 'swal2-cancel inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold ml-2'
          }
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    });
  });
</script>
@endsection

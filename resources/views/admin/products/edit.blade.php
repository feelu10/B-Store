@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold text-purple-700 mb-4">Edit Product</h1>

<form id="product-update-form"
      action="{{ route('admin.products.update',$product) }}"
      method="POST"
      enctype="multipart/form-data"
      class="bg-white rounded shadow p-4 grid md:grid-cols-2 gap-4">
  @csrf @method('PUT')

  <div>
    <label class="block text-sm text-gray-600 mb-1">Name</label>
    <input name="name" value="{{ old('name',$product->name) }}" class="w-full border rounded px-3 py-2" required>
    @error('name') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
  </div>

  <div>
    <label class="block text-sm text-gray-600 mb-1">Category</label>
    <select name="category_id" class="w-full border rounded px-3 py-2">
      <option value="">— None —</option>
      @foreach($categories as $c)
        <option value="{{ $c->id }}" @selected($product->category_id==$c->id)>{{ $c->name }}</option>
      @endforeach
    </select>
    @error('category_id') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
  </div>

  <div>
    <label class="block text-sm text-gray-600 mb-1">Price</label>
    <input type="number" step="0.01" name="price" value="{{ old('price',$product->price) }}" class="w-full border rounded px-3 py-2" required>
    @error('price') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
  </div>

  <div>
    <label class="block text-sm text-gray-600 mb-1">Stock</label>
    <input type="number" name="stock" value="{{ old('stock',$product->stock) }}" class="w-full border rounded px-3 py-2" required>
    @error('stock') <div class="text-pink-600 text-sm">{{ $message }}</div> @enderror
  </div>

  <div class="md:col-span-2">
    <label class="block text-sm text-gray-600 mb-1">Short Description</label>
    <input name="short_description" value="{{ old('short_description',$product->short_description) }}" class="w-full border rounded px-3 py-2">
  </div>

  <div class="md:col-span-2">
    <label class="block text-sm text-gray-600 mb-1">Description</label>
    <textarea name="description" rows="5" class="w-full border rounded px-3 py-2">{{ old('description',$product->description) }}</textarea>
  </div>

  {{-- Existing images (NO forms inside!) --}}
  <div class="md:col-span-2">
    <label class="block text-sm text-gray-600 mb-2">Existing Images</label>
    @if($product->images->count())
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach($product->images as $img)
          <div class="relative group">
            <img src="{{ asset('storage/'.$img->path) }}"
                 alt="Product image"
                 class="w-full h-28 object-cover rounded shadow cursor-pointer existing-thumb"
                 data-src="{{ asset('storage/'.$img->path) }}">
            {{-- This button submits the hidden form below via the "form" attribute --}}
            <button type="submit"
                    form="delimg-{{ $img->id }}"
                    class="absolute top-1 right-1 bg-red-600 text-white text-xs px-2 py-1 rounded hidden group-hover:block"
                    onclick="return confirm('Remove this image?')">✕</button>
          </div>
        @endforeach
      </div>
    @else
      <p class="text-sm text-gray-500">No images yet.</p>
    @endif
  </div>

  {{-- Add new images with preview --}}
  <div class="md:col-span-2">
    <label class="block text-sm text-gray-600 mb-1">Add New Images</label>
    <input type="file" id="imagesInput" name="images[]" multiple class="w-full">
    <p class="text-xs text-gray-500">You can select multiple images (JPG, PNG, max 2MB each)</p>
    @error('images.*') <div class="text-pink-600 text-sm mt-1">{{ $message }}</div> @enderror

    <div id="previewContainer" class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3"></div>
  </div>

  {{-- Safe boolean: send 0 when unchecked --}}
  <div class="flex items-center gap-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active',$product->is_active))>
    <span>Active</span>
  </div>

  <div class="md:col-span-2 flex gap-2">
    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded border">Cancel</a>
    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded">Update</button>
  </div>
</form>

{{-- Hidden delete forms (outside the main form!) --}}
@foreach($product->images as $img)
  <form id="delimg-{{ $img->id }}"
        action="{{ route('admin.products.images.destroy', [$product,$img]) }}"
        method="POST" class="hidden">
    @csrf @method('DELETE')
  </form>
@endforeach

{{-- Modal for larger preview --}}
<div id="imageModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-lg p-4 max-w-3xl w-full">
    <button id="closeModal" class="ml-auto block text-gray-600 hover:text-gray-900 mb-2">✕</button>
    <img id="modalImage" src="" alt="Preview" class="max-h-[80vh] mx-auto rounded">
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Modal
  const modal = document.getElementById('imageModal');
  const modalImage = document.getElementById('modalImage');
  const closeModal = document.getElementById('closeModal');

  document.querySelectorAll('.existing-thumb').forEach(el => {
    el.addEventListener('click', () => {
      modalImage.src = el.dataset.src;
      modal.classList.remove('hidden'); modal.classList.add('flex');
    });
  });

  const input = document.getElementById('imagesInput');
  const previewContainer = document.getElementById('previewContainer');
  let filesArray = [];

  input?.addEventListener('change', () => {
    filesArray = Array.from(input.files);
    renderPreviews();
  });

  function renderPreviews() {
    previewContainer.innerHTML = '';
    filesArray.forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = e => {
        const div = document.createElement('div');
        div.className = "relative group";
        div.innerHTML = `
          <img src="${e.target.result}" class="w-full h-28 object-cover rounded shadow cursor-pointer" data-index="${index}">
          <button type="button" class="absolute top-1 right-1 bg-red-600 text-white text-xs px-2 py-1 rounded hidden group-hover:block" data-remove="${index}">✕</button>
        `;
        previewContainer.appendChild(div);
      };
      reader.readAsDataURL(file);
    });
  }

  previewContainer?.addEventListener('click', e => {
    if (e.target.dataset.remove !== undefined) {
      const i = parseInt(e.target.dataset.remove);
      filesArray.splice(i, 1);
      const dt = new DataTransfer();
      filesArray.forEach(f => dt.items.add(f));
      input.files = dt.files;
      renderPreviews();
    }
    if (e.target.dataset.index !== undefined) {
      modalImage.src = e.target.src;
      modal.classList.remove('hidden'); modal.classList.add('flex');
    }
  });

  closeModal.addEventListener('click', () => { modal.classList.add('hidden'); modal.classList.remove('flex'); });
  modal.addEventListener('click', (e) => { if (e.target === modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }});
});
</script>
@endpush

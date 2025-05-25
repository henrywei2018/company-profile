<x-layouts.admin>
    <div class="container">
        <h1 class="text-2xl font-bold mb-4">Edit Category</h1>
        <form action="{{ route('postCategories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block font-semibold">Name</label>
                <input type="text" name="name" id="name" class="w-full border rounded p-2" value="{{ old('name', $category->name) }}">
            </div>

            <div class="mb-4">
                <label for="slug" class="block font-semibold">Slug</label>
                <input type="text" name="slug" id="slug" class="w-full border rounded p-2" value="{{ old('slug', $category->slug) }}">
            </div>

            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Update</button>
        </form>
    </div>
</x-layouts.admin>
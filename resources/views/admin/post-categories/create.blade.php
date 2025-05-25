<x-layouts.admin>
    <div class="container">
        <h1 class="text-2xl font-bold mb-4">Create New Category</h1>
        <form action="{{ route('postCategories.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block font-semibold">Name</label>
                <input type="text" name="name" id="name" class="w-full border rounded p-2" value="{{ old('name') }}">
            </div>

            <div class="mb-4">
                <label for="slug" class="block font-semibold">Slug</label>
                <input type="text" name="slug" id="slug" class="w-full border rounded p-2" value="{{ old('slug') }}">
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create</button>
        </form>
    </div>
</x-layouts.admin>
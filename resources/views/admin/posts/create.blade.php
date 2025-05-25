<x-layouts.admin>
    <div class="container">
        <h1 class="text-2xl font-bold mb-4">Create New Post</h1>
        <form action="{{ route('posts.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="title" class="block font-semibold">Title</label>
                <input type="text" name="title" id="title" class="w-full border rounded p-2" value="{{ old('title') }}">
            </div>

            <div class="mb-4">
                <label for="slug" class="block font-semibold">Slug</label>
                <input type="text" name="slug" id="slug" class="w-full border rounded p-2" value="{{ old('slug') }}">
            </div>

            <div class="mb-4">
                <label for="content" class="block font-semibold">Content</label>
                <textarea name="content" id="content" rows="5" class="w-full border rounded p-2">{{ old('content') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="thumbnail" class="block font-semibold">Thumbnail URL</label>
                <input type="text" name="thumbnail" id="thumbnail" class="w-full border rounded p-2" value="{{ old('thumbnail') }}">
            </div>

            <div class="mb-4">
                <label for="categories" class="block font-semibold">Categories</label>
                <select name="categories[]" id="categories" multiple class="w-full border rounded p-2">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create</button>
        </form>
    </div>
</x-layouts.admin>
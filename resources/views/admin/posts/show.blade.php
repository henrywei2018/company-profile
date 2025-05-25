<x-layouts.admin>
    <div class="container">
        <h1 class="text-2xl font-bold mb-4">{{ $post->title }}</h1>

        <p class="mb-2 text-gray-700">Slug: <strong>{{ $post->slug }}</strong></p>
        <p class="mb-2">Categories:
            @foreach ($post->categories as $category)
                <span class="bg-gray-300 rounded px-2 py-1 text-sm inline-block mr-1">{{ $category->name }}</span>
            @endforeach
        </p>
        <p class="mb-4">{!! nl2br(e($post->content)) !!}</p>

        @if ($post->thumbnail)
            <div class="mb-4">
                <img src="{{ $post->thumbnail }}" alt="Thumbnail" class="w-64 rounded">
            </div>
        @endif

        <a href="{{ route('posts.edit', $post) }}" class="bg-yellow-500 text-white px-4 py-2 rounded">Edit</a>
        <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded" onclick="return confirm('Are you sure?')">Delete</button>
        </form>
    </div>
</x-layouts.admin>
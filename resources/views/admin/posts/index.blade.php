<x-layouts.admin>
    <div class="container">
        <h1 class="text-2xl font-bold mb-4">All Posts</h1>
        <a href="{{ route('posts.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Create New Post</a>

        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">#</th>
                    <th class="border px-4 py-2">Title</th>
                    <th class="border px-4 py-2">Categories</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                <tr>
                    <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                    <td class="border px-4 py-2">{{ $post->title }}</td>
                    <td class="border px-4 py-2">
                        @foreach ($post->categories as $category)
                            <span class="bg-gray-200 px-2 py-1 rounded text-sm">{{ $category->name }}</span>
                        @endforeach
                    </td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('posts.show', $post) }}" class="text-blue-500">View</a> |
                        <a href="{{ route('posts.edit', $post) }}" class="text-green-500">Edit</a> |
                        <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500" onclick="return confirm('Delete this post?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>
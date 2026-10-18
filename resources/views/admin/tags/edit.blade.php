<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-gray-700">タグ編集</h2>
    </x-slot>

    <div class="min-h-screen bg-white py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <h3 class="text-center text-2xl font-serif text-amber-900 mb-6">
                タグ名を編集
            </h3>

            <form action="{{ route('admin.tags.update', $tag->id) }}" method="post"
                class="bg-[#faf7f4] rounded p-6">
                @csrf
                @method('PUT')

                <label for="tag-name" class="block text-sm text-gray-700 mb-2">
                    タグ名
                </label>

                <input
                    type="text"
                    id="tag-name"
                    name="name"
                    value="{{ old('name', $tag->name) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-amber-500"
                />

                @error('name')
                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                @enderror

                <div class="flex items-center gap-3 mt-6">
                    <a href="{{ route('admin.index') }}" class="px-6 py-2 bg-[#e8dfd5] text-[#8b8178] rounded hover:bg-[#ddd1c5]">
                        戻る
                    </a>

                    <button type="submit" class="px-6 py-2 bg-[#817770] text-white rounded hover:bg-[#706660]">
                        更新
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

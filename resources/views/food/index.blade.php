<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Food') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-10">
                <a href="{{ route('food.create') }}"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Create Food
                </a>
            </div>
            <div class="overflow-hidden rounded-lg border border-gray-200 shadow-md m-5">
                <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                    <thead>
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 text-center">ID</th>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 text-center">Name</th>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 text-center">Ingredients</th>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 text-center">Price</th>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 text-center">Rate</th>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 border-t border-gray-900">
                        @forelse ($food as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-center">{{ $item->id }}</td>
                                <td class="px-6 py-4 font-normal text-gray-900">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-700">{{ $item->name }}</div>
                                        <div class="text-gray-400">{{ $item->types }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ $item->ingredients }}
                                </td>

                                <td class="px-6 py-4 text-center">IDR {{ number_format($item->price) }}</td>
                                <td class="px-6 py-4 text-center">{{ number_format($item->rate, 1) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('food.edit', $item->id) }}"
                                        class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mx-2 rounded">Edit</a>
                                    <form action="{{ route('food.destroy', $item->id) }}" method="POST"
                                        class="inline-block">
                                        {!! method_field('delete') . csrf_field() !!}
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 mx-2 rounded">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-5">
                                    Data User Tidak Ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="div text-center mt-5">
                {{ $food->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

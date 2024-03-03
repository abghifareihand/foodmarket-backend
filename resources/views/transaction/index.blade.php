<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg border border-gray-200 shadow-md m-5">
                <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                    <thead>
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 text-center">ID</th>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 text-center">Food</th>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 text-center">User</th>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 text-center">Quantity</th>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 text-center">Total</th>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 text-center">Status</th>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 text-center">Date</th>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 border-t border-gray-900">
                        @forelse ($transaction as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-center font-medium text-gray-900">{{ $item->id }}</td>
                                <td class="px-6 py-4 font-normal text-gray-900">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-700">{{ $item->food->name }}</div>
                                        <div class="text-gray-400">{{ $item->food->types }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">{{ $item->user->name }}</td>
                                <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-center">
                                    IDR {{ number_format($item->total) }}
                                </td>

                                <td
                                    class="px-6 py-4 text-center font-semibold {{ $item->status === 'SUCCESS' ? 'text-green-600' : ($item->status === 'PENDING' ? 'text-red-600' : '') }}">
                                    {{ $item->status }}
                                </td>

                                <td class="px-6 py-4 text-center">{{ $item->created_at }}</td>

                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('transaction.show', $item->id) }}"
                                        class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mx-2 rounded">Show</a>
                                    <form action="{{ route('transaction.destroy', $item->id) }}" method="POST"
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
                                    Data Transaksi Tidak Ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="div text-center mt-5">
                {{ $transaction->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

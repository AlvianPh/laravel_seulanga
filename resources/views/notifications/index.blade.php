<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Notifikasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Semua Notifikasi</h3>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <form action="{{ route('notifications.read-all') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-medium bg-indigo-50 dark:bg-indigo-900/50 px-3 py-1.5 rounded-lg transition-colors">
                                    Tandai Semua Dibaca
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="space-y-4">
                        @forelse($notifications as $notification)
                            <div class="flex items-start p-4 border rounded-xl {{ is_null($notification->read_at) ? 'bg-indigo-50/30 border-indigo-200 dark:bg-indigo-900/20 dark:border-indigo-800' : 'bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700' }}">
                                
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-base font-semibold {{ is_null($notification->read_at) ? 'text-gray-900 dark:text-gray-100' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $notification->data['title'] ?? 'Notifikasi' }}
                                        </h4>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="mt-1 text-sm {{ is_null($notification->read_at) ? 'text-gray-800 dark:text-gray-200' : 'text-gray-600 dark:text-gray-400' }}">
                                        {{ $notification->data['message'] ?? '' }}
                                    </p>
                                    <div class="mt-3 flex items-center gap-3">
                                        @if(isset($notification->data['url']))
                                            <a href="{{ $notification->data['url'] }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                                Lihat Detail
                                            </a>
                                        @endif
                                        
                                        @if(is_null($notification->read_at))
                                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                                    Tandai Dibaca
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                
                                @if(is_null($notification->read_at))
                                    <div class="ml-4 w-2 h-2 mt-2 bg-red-500 rounded-full"></div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi untuk Anda.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

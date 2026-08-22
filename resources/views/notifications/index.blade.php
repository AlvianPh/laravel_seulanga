<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Notifikasi') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <x-ui.card>
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Pusat Notifikasi</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pemberitahuan sistem otomatis terkait tagihan, kontrak sewa, dan operasional kost</p>
                    </div>
                    @if(Auth::user()->unreadNotifications->count() > 0)
                        <form action="{{ route('notifications.read-all') }}" method="POST">
                            @csrf
                            <x-ui.button size="sm" variant="secondary" type="submit">
                                Tandai Semua Dibaca
                            </x-ui.button>
                        </form>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse($notifications as $notification)
                        <div class="flex items-start p-4 border rounded-2xl transition-colors {{ is_null($notification->read_at) ? 'bg-indigo-50/40 border-indigo-200/80 dark:bg-indigo-950/30 dark:border-indigo-800/60' : 'bg-white border-gray-200/80 dark:bg-gray-800 dark:border-gray-700/70' }}">
                            
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-bold {{ is_null($notification->read_at) ? 'text-indigo-950 dark:text-indigo-200' : 'text-gray-800 dark:text-gray-200' }}">
                                        {{ $notification->data['title'] ?? 'Notifikasi' }}
                                    </h4>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1 text-sm {{ is_null($notification->read_at) ? 'text-gray-800 dark:text-gray-200' : 'text-gray-600 dark:text-gray-400' }}">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                                <div class="mt-3 flex items-center gap-2">
                                    @if(isset($notification->data['url']))
                                        <x-ui.button size="sm" variant="secondary" href="{{ $notification->data['url'] }}">
                                            Lihat Detail
                                        </x-ui.button>
                                    @endif
                                    
                                    @if(is_null($notification->read_at))
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <x-ui.button size="sm" variant="secondary" type="submit">
                                                Tandai Dibaca
                                            </x-ui.button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            
                            @if(is_null($notification->read_at))
                                <div class="ml-3 w-2.5 h-2.5 mt-1.5 bg-indigo-600 rounded-full animate-pulse"></div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700/60 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            </div>
                            <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">Tidak Ada Notifikasi</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Semua aktivitas dan pemberitahuan sistem terkendali.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>

        </x-ui.card>
    </div>
</x-app-layout>

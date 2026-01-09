<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Bulan --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Bulan
                    </label>
                    <select wire:model.live="month" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tahun
                    </label>
                    <select wire:model.live="year" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        @for ($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        {{-- Ranking Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($rankings as $index => $ranking)
                @php
                    $rank = $index + 1;
                    $host = $ranking['host'];
                    $score = $ranking['score'];
                    $status = $ranking['status'];
                    
                    $medalIcon = match($rank) {
                        1 => '🥇',
                        2 => '🥈',
                        3 => '🥉',
                        default => null
                    };
                    
                    $statusColor = match($status) {
                        'OK' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                        'WARNING' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                        'DROP' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                    };
                    
                    $borderColor = match($rank) {
                        1 => 'border-yellow-400',
                        2 => 'border-gray-400',
                        3 => 'border-orange-400',
                        default => 'border-gray-200 dark:border-gray-700'
                    };
                @endphp
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border-2 {{ $borderColor }} p-6 hover:shadow-xl transition-shadow">
                    {{-- Rank Badge --}}
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-3xl font-bold">
                            @if ($medalIcon)
                                <span class="text-4xl">{{ $medalIcon }}</span>
                            @else
                                <span class="text-gray-600 dark:text-gray-400">#{{ $rank }}</span>
                            @endif
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusColor }}">
                            {{ $status }}
                        </span>
                    </div>
                    
                    {{-- Host Info --}}
                    <div class="flex items-center space-x-4 mb-4">
                        @if ($host->photo_path)
                            <img src="{{ asset('storage/' . $host->photo_path) }}" 
                                 alt="{{ $host->name }}" 
                                 class="w-16 h-16 rounded-full object-cover border-2 border-gray-300">
                        @else
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-2xl font-bold">
                                {{ substr($host->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $host->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $host->role }}</p>
                        </div>
                    </div>
                    
                    {{-- Score --}}
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Score</span>
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($score, 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min($score, 100) }}%"></div>
                        </div>
                    </div>
                    
                    {{-- KPI Metrics --}}
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total GMV:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($ranking['total_gmv'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">GMV/Jam:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($ranking['gmv_per_hour'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total Jam:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($ranking['total_hours'], 2) }} jam</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if (empty($rankings))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                <div class="text-gray-400 dark:text-gray-600 text-6xl mb-4">📊</div>
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Belum Ada Data</h3>
                <p class="text-gray-500 dark:text-gray-400">Tidak ada data ranking untuk bulan yang dipilih.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>

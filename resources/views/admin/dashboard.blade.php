<x-app-admin>
    <main class="overflow-y-auto">
        <h1 class="text-2xl font-bold text-blue-950 mb-6">
            {{ $currentBatch?->name ? $currentBatch->name.' (Periode Saat Ini)' : 'Batch (belum ada yang Active)' }}
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="grid grid-cols-2 gap-4 mb-6">
                @forelse($positionCards->take(10) as $card)
                    <div class="bg-white p-4 rounded-2xl shadow-zinc-400/50">
                        <div class="flex items-center gap-4">
                            <div class="bg-orange-400 w-12 h-12 flex items-center justify-center rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4 text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-700 mt-1">{{ $card['name'] }}</p>
                                <h2 class="text-2xl font-bold">{{ $card['count'] }}</h2>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-gray-500">Belum ada posisi pada batch aktif.</div>
                @endforelse
            </div>

            <div class="grid grid-cols-1 gap-6 mb-6">
                <div>
                    <h1 class="text-xl font-semibold text-blue-950 mb-3">
                        Limit Kuota Pelamar {{ $currentBatch?->name ?? '-' }}
                    </h1>
                    <div class="bg-white p-4 rounded-2xl shadow-zinc-400/50">
                        <div class="flex items-center gap-4">
                            <div class="bg-orange-400 p-5 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6 text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-xl mb-2 font-semibold">
                                    {{ $quota['applied'] }}/{{ $quota['limit'] }}
                                </h2>
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="bg-orange-400 h-4 rounded-full"
                                        style="width: {{ $quota['percent'] }}%">
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700 mt-1">
                                    {{ $quota['percent'] >= 90 ? 'Hampir Penuh!' : ($quota['percent'] >= 50 ? 'Terisi' : 'Masih banyak kuota') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
            <div>
                <h1 class="text-xl font-semibold text-blue-950 mb-3">Grafik Monitoring Pelamar 6 Bulan Terakhir</h1>
                <div class="bg-white p-4 rounded-2xl shadow-zinc-400/50">
                    <div id="chart"></div>
                </div>
            </div>

            <div>
                <h1 class="text-xl font-semibold text-blue-950 mb-3">Persentase Job yang dilamar</h1>
                <div class="bg-white p-4 rounded-2xl shadow-zinc-400/50">
                    <div id="piechart"></div>
                </div>
            </div>
        </div>

        {{-- === BAGIAN JUMLAH PESERTA PER TAHAP === --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach ([
                'Administration' => $progress['admin'],
                'Quiz (Tes Tulis)' => $progress['quiz'],
                'Technical Test' => $progress['tech'],
                'Interview' => $progress['interview'],
                'Offering' => $progress['offering']
            ] as $title => $count)
                <div class="flex flex-col justify-between h-32 p-4 bg-white rounded-2xl shadow-zinc-400/50">
                    <div>
                        <p class="text-sm text-gray-800 mb-2">{{ $title }}</p>
                        <div class="flex items-baseline">
                            <h2 class="text-lg font-semibold me-1">{{ $count }}</h2>
                            <span class="text-zinc-400">Applicant</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    @if(($quota['applied'] ?? 0) === 0)
        <div class="flex-col justify-items-center md:p-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                class="size-14 text-red-600 mx-auto">
                <path fill-rule="evenodd"
                    d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                    clip-rule="evenodd" />
            </svg>
            <h1 class="font-bold text-2xl mt-1 text-center">Tidak dapat menampilkan data</h1>
            <p class="text-gray-400 text-lg text-center">Silahkan menambahkan data terlebih dahulu agar sistem dapat menampilkan data</p>
        </div>
    @endif

    {{-- Tambahkan di atas script grafik kamu --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        const chartData = @json($chartData);
        const pieData = @json($pieData);

        var chart = new ApexCharts(document.querySelector("#chart"), {
            chart: { type: 'bar', height: 350 },
            series: chartData.series,
            xaxis: { categories: chartData.months },
            dataLabels: { enabled: false },
            legend: { position: 'bottom' },
            colors: ['#00bcd4', '#2196f3', '#9c27b0']
        });
        chart.render();

        var piechart = new ApexCharts(document.querySelector("#piechart"), {
            chart: { type: 'pie', height: 350 },
            labels: pieData.labels,
            series: pieData.series,
            legend: { position: 'right' },
            colors: ['#ff9800', '#00bcd4', '#9c27b0', '#4caf50', '#f44336']
        });
        piechart.render();
    </script>

</x-app-admin>

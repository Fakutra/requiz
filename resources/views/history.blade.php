<x-guest-layout>
  <div class="py-12">
    <div class="max-w-7xl mx-auto px-6 sm:px-6 lg:px-8">
      <div>
        <h1 class="mb-4 font-bold text-4xl">Riwayat Lamaran</h1>
      </div>
      <div class="bg-white shadow sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            @forelse ($applicants as $applicant)
            <div class="h-full rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow">
              <div class="p-5">
                <h3 class="text-lg font-semibold text-gray-900">
                  {{ $applicant->position->name ?? '-' }}
                </h3>

                <p class="mt-1 text-sm text-gray-600">
                  Dilamar pada :
                  <span class="font-medium text-gray-800">
                    {{ $applicant->created_at->format('l, d F Y') }}
                  </span>
                </p>

                <div class="mt-2 text-sm">
                  Status : {!! $applicant->status !!}
                </div>
              </div>
            </div>
            @empty
            <div class="col-span-full text-center text-sm text-gray-500 py-10">
              Belum ada lamaran.
            </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
</x-guest-layout>

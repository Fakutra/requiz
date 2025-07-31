<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lamaran Saya') }}
        </h2>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="row">
                        @foreach ($applicants as $applicant)
                            <div class="col-md-12 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $applicant->position->name ?? '-' }}</h5>
                                        <p class="card-text">Dilamar pada :
                                            {{ $applicant->created_at->format('l, d F Y') }}</p>
                                        <p class="card-text">Status : {!! $applicant->status !!}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

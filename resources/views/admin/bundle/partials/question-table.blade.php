{{-- File ini hanya berisi tabel dan link paginasi --}}
{{-- <div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Soal</th>
                <th>Tipe</th>
                <th style="width: 10%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($questionsInBundle as $question)
                <tr>
                    <td>{{ Str::limit($question->question, 80) }}</td>
                    <td><span class="badge bg-info">{{ $question->type }}</span></td>
                    <td>
                        <form action="{{ route('bundle.questions.remove', ['bundle' => $bundle->id, 'question' => $question->id]) }}" method="POST" class="delete-question-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">Belum ada soal di bundle ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div> --}}

{{-- Link Paginasi --}}
{{-- <div class="mt-4">
    {{ $questionsInBundle->links() }}
</div> --}}

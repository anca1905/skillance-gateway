@extends('layouts.main')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-primary mb-0"><i class="fas fa-robot me-2"></i>Auto Reply</h3>
                <small class="text-muted">Setting bot jawaban otomatis untuk device Anda.</small>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReplyModal">
                <i class="fas fa-plus me-2"></i>Tambah Keyword
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="p-3">Device</th>
                            <th>Keyword (Kata Kunci)</th>
                            <th>Jenis Pencarian</th>
                            <th>Balasan Bot</th>
                            <th class="text-end p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($autoreplies as $reply)
                            <tr>
                                <td class="p-3">
                                    <span class="badge bg-secondary">{{ $reply->device->label }}</span>
                                </td>
                                <td class="fw-bold text-primary">{{ $reply->keyword }}</td>
                                <td>
                                    @if ($reply->search_type == 'exact')
                                        <span class="badge bg-info text-dark">Persis (Exact)</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Mengandung Kata (Contains)</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-muted small text-truncate" style="max-width: 300px;">
                                        {{ $reply->response }}
                                    </div>
                                </td>
                                <td class="text-end p-3">
                                    <form action="{{ route('autoreply.destroy', $reply->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus keyword ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        @if ($autoreplies->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada keyword bot.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addReplyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Auto Reply</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('autoreply.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Device</label>
                            <select name="device_id" class="form-select" required>
                                @foreach ($devices as $d)
                                    <option value="{{ $d->id }}">{{ $d->label }} ({{ $d->nomor_hp }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Keyword</label>
                                <input type="text" name="keyword" class="form-control" placeholder="Contoh: info"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tipe Match</label>
                                <select name="search_type" class="form-select">
                                    <option value="exact">Persis</option>
                                    <option value="contains">Mengandung</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jawaban Bot</label>
                            <textarea name="response" class="form-control" rows="4" placeholder="Halo, ada yang bisa dibantu?" required></textarea>
                            <div class="form-text">Bisa menggunakan emoji 😃</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

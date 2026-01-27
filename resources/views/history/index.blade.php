@extends('layouts.main')

@section('content')
    <div class="container mt-4">
        <h3 class="fw-bold text-primary mb-4"><i class="fas fa-history me-2"></i>Riwayat Pesan</h3>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="p-3">Waktu</th>
                            <th>Pengirim (Device)</th>
                            <th>Tujuan</th>
                            <th>Pesan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($histories as $log)
                            <tr>
                                <td class="p-3 text-muted small">
                                    {{ $log->created_at->format('d M Y H:i') }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $log->device->label }}</span>
                                </td>
                                <td class="fw-bold">{{ $log->number }}</td>
                                <td>
                                    <div class="text-truncate" style="max-width: 250px;" title="{{ $log->message }}">
                                        {{ $log->message }}
                                    </div>
                                </td>
                                <td>
                                    @if ($log->status == 'success')
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i> Terkirim</span>
                                    @else
                                        <span class="badge bg-danger" title="{{ $log->error_log }}">
                                            <i class="fas fa-times me-1"></i> Gagal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @if ($histories->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pesan.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div class="p-3">
                    {{ $histories->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

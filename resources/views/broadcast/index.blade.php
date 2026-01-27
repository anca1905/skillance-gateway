@extends('layouts.main')

@section('content')
    <div class="container mt-4">
        <h3 class="fw-bold text-primary mb-4"><i class="fas fa-bullhorn me-2"></i>Broadcast Pesan</h3>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Buat Kampanye Baru</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('broadcast.send') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Pengirim (Device)</label>
                                <select name="device_id" class="form-select" required>
                                    @foreach ($devices as $d)
                                        <option value="{{ $d->id }}">
                                            {{ $d->label }} ({{ $d->nomor_hp }}) - Sisa Kuota: {{ $d->quota }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($devices->isEmpty())
                                    <small class="text-danger">*Tidak ada device yang Connected. Silakan scan dulu di menu
                                        Device.</small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Target Nomor</label>
                                <textarea name="numbers" class="form-control" rows="5" placeholder="08123456789&#10;08567891234&#10;62811223344"
                                    required></textarea>
                                <div class="form-text">Masukkan satu nomor per baris.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Isi Pesan</label>
                                <textarea name="message" class="form-control" rows="4" placeholder="Halo kak, jangan lupa..." required></textarea>
                                <div class="form-text">Tips: Hindari kata-kata spam agar nomor tidak diblokir.</div>
                            </div>

                            <div class="alert alert-warning small">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Sistem akan mengirim pesan dengan <strong>Jeda Acak (3-6 detik)</strong> untuk keamanan
                                nomor Anda.
                            </div>

                            <button type="submit" class="btn btn-primary w-100"
                                {{ $devices->isEmpty() ? 'disabled' : '' }}>
                                <i class="fas fa-paper-plane me-2"></i> Kirim Broadcast
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-info text-white border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold"><i class="fas fa-shield-alt me-2"></i>Anti-Banned System</h5>
                        <p class="small">Skillance Gateway menggunakan algoritma "Human Behavior" (Perilaku Manusia).</p>
                        <ul class="small ps-3">
                            <li>Delay acak antar pesan.</li>
                            <li>Verifikasi nomor sebelum kirim.</li>
                            <li>Proses berjalan di background.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

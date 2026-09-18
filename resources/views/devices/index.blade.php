@extends('layouts.main')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-primary mb-0"><i class="fab fa-whatsapp me-2"></i>Kelola Device</h3>
                <small class="text-muted">Pantau koneksi, kuota, dan masa aktif device Anda.</small>
            </div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
                <i class="fas fa-plus me-2"></i>Tambah Device
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-uppercase small text-muted">
                            <tr>
                                <th class="p-3">Device Info</th>
                                <th>Paket & Kuota</th>
                                <th>API Token</th>
                                <th>Status</th>
                                <th class="text-end p-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @foreach ($devices as $device)
                                <tr>
                                    <td class="p-3">
                                        <div class="fw-bold text-dark">{{ $device->label }}</div>
                                        <div class="small text-muted">
                                            <i class="fas fa-phone-alt me-1 text-secondary"></i>
                                            {{ $device->nomor_hp ?? 'Belum Scan QR' }}
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <div class="small">
                                                <i class="fas fa-calendar-check me-2 text-primary"></i>
                                                @if ($device->expired_date)
                                                    <span
                                                        class="{{ $device->expired_date < date('Y-m-d') ? 'text-danger fw-bold' : 'text-success' }}">
                                                        Exp: {{ date('d M Y', strtotime($device->expired_date)) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Trial</span>
                                                @endif
                                            </div>

                                            <div class="small">
                                                <i class="fas fa-robot me-2 text-info"></i>
                                                Kuota: <strong>{{ $device->quota }}</strong> Pesan
                                            </div>

                                            <div class="progress mt-1" style="height: 6px; width: 140px;">
                                                @php
                                                    // Asumsi paket default 1000 (untuk visualisasi persen)
                                                    $max = 1000;
                                                    $percent = ($device->quota / $max) * 100;
                                                    $color = $percent < 20 ? 'bg-danger' : 'bg-success';
                                                @endphp
                                                <div class="progress-bar {{ $color }}" role="progressbar"
                                                    style="width: {{ $percent }}%"></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="input-group input-group-sm" style="width: 220px;">
                                            <span class="input-group-text bg-light"><i class="fas fa-key"></i></span>
                                            <input type="text" class="form-control bg-light text-muted"
                                                value="{{ $device->api_token }}" readonly>
                                            <button class="btn btn-outline-secondary"
                                                onclick="copyToken('{{ $device->api_token }}')" title="Copy Token">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>

                                    <td>
                                        @if ($device->status == 'connected')
                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                <i class="fas fa-wifi me-1"></i> Connected
                                            </span>
                                        @else
                                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                                <i class="fas fa-unlink me-1"></i> Disconnected
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-end p-3">
                                        <button class="btn btn-sm btn-primary shadow-sm me-1"
                                            onclick="scanQR('{{ $device->api_token }}')">
                                            <i class="fas fa-qrcode me-1"></i> Scan
                                        </button>
                                        <button class="btn btn-sm btn-warning text-white shadow-sm me-1"
                                            onclick="editDevice({{ $device->id }}, '{{ $device->label }}', '{{ $device->webhook_url }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form action="{{ route('devices.destroy', $device->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin hapus device ini? Token akan hangus.');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger shadow-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                            @if ($devices->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80"
                                            class="mb-3 opacity-50">
                                        <p class="mb-0">Belum ada device. Tambahkan device baru untuk mulai.</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addDeviceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Device Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('devices.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Label Device</label>
                            <input type="text" name="label" class="form-control"
                                placeholder="Contoh: Admin Toko / CS 1" required>
                            <div class="form-text">Beri nama agar mudah dikenali.</div>
                        </div>
                        <div class="alert alert-info small mb-0">
                            <i class="fas fa-gift me-2"></i>Device baru akan mendapatkan <strong>Paket Trial</strong> (10
                            Kuota / 3 Hari).
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan & Generate Token</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="scanModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered text-center">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pb-5">
                    <h5 class="fw-bold mb-3">Scan QR Code</h5>
                    <div class="bg-light p-3 d-inline-block rounded border mb-3">
                        <div id="qrcode-container"></div>
                        <div id="spinner-scan" class="spinner-border text-primary mt-2" role="status"
                            style="display:none;"></div>
                    </div>
                    <p class="text-muted mb-0" id="scan-status">Menghubungkan ke Server Node.js...</p>
                    <small class="text-danger fw-bold d-block mt-2" id="timer-text"></small>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editDeviceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Label Device</label>
                            <input type="text" name="label" id="edit_label" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Webhook URL (Opsional)</label>
                            <input type="url" name="webhook_url" id="edit_webhook" class="form-control"
                                placeholder="https://domain-kamu.com/api/webhook">
                            <div class="form-text small">Masukkan URL API untuk memproses pesan masuk secara otomatis.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editDevice(id, label, webhook) {
            // Set Action Form URL secara dinamis
            document.getElementById('editForm').action = '/devices/' + id;

            // Isi inputan dengan data lama
            document.getElementById('edit_label').value = label;
            document.getElementById('edit_webhook').value = webhook || ''; // Kalau null, isi string kosong

            // Tampilkan Modal
            new bootstrap.Modal(document.getElementById('editDeviceModal')).show();
        }
    </script>

    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        const socket = io("http://localhost:5000"); // Sesuaikan Port Node.js
        let currentToken = "";

        function scanQR(token) {
            currentToken = token;
            const modalEl = document.getElementById('scanModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            // Reset Tampilan
            document.getElementById('qrcode-container').innerHTML = "";
            document.getElementById('spinner-scan').style.display = 'block';
            document.getElementById('scan-status').innerText = "Meminta sesi baru...";
            document.getElementById('scan-status').className = "text-muted mb-0";

            // 1. Join Room
            socket.emit('join_room', token);

            // 2. Request Init Session
            fetch('http://localhost:5000/init', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        token: token
                    })
                })
                .then(res => res.json())
                .then(data => {
                    console.log("Server response:", data);
                })
                .catch(err => {
                    document.getElementById('scan-status').innerText = "Gagal konek ke Node.js (Cek Terminal!)";
                    document.getElementById('scan-status').className = "text-danger fw-bold";
                    document.getElementById('spinner-scan').style.display = 'none';
                });
        }

        // --- EVENT LISTENER SOCKET ---

        // 1. Terima QR Code
        socket.on('qr', (data) => {
            if (data.token === currentToken) {
                document.getElementById('spinner-scan').style.display = 'none';
                document.getElementById('qrcode-container').innerHTML = "";

                new QRCode(document.getElementById('qrcode-container'), {
                    text: data.qr,
                    width: 220,
                    height: 220
                });

                document.getElementById('scan-status').innerText = "Silakan Scan QR Code di HP Anda";
                document.getElementById('scan-status').className = "text-primary fw-bold";
            }
        });

        // 2. Sudah Connect (Ready)
        socket.on('ready', (data) => {
            if (data.token === currentToken) {
                bootstrap.Modal.getInstance(document.getElementById('scanModal')).hide();

                // Sweet Alert sederhana (Pakai alert biasa dulu)
                alert("✅ WhatsApp Berhasil Terhubung! Device Siap.");
                location.reload();
            }
        });

        function copyToken(text) {
            navigator.clipboard.writeText(text);
            alert("Token API berhasil disalin!");
        }
    </script>
@endsection

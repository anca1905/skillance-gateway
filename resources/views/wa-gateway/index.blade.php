@extends('layouts.main')

@section('title', 'Hubungkan WhatsApp')
@section('header', 'WhatsApp Gateway')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="fab fa-whatsapp me-2"></i> Device Connection
                </div>
                <div class="card-body text-center p-5">

                    <div id="status-area" class="mb-4">
                        <div class="spinner-border text-success mb-3" role="status" id="loading-spinner"
                            style="display: none;"></div>
                        <h4 id="status-text" class="fw-bold text-secondary">Disconnected</h4>
                        <p class="text-muted small" id="status-desc">Klik tombol di bawah untuk menghubungkan nomor
                            WhatsApp.</p>
                    </div>

                    <div id="qr-area" class="d-flex justify-content-center mb-4" style="display: none !important;">
                        <div class="p-3 bg-white border rounded shadow-sm">
                            <div id="qrcode"></div>
                        </div>
                    </div>

                    <button onclick="startSession()" id="btn-scan" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-qrcode me-2"></i> Scan QR Code
                    </button>

                    <button onclick="logoutSession()" id="btn-logout" class="btn btn-outline-danger w-100 py-2 mt-2"
                        style="display: none;">
                        <i class="fas fa-sign-out-alt me-2"></i> Putuskan Koneksi
                    </button>

                </div>
                <div class="card-footer bg-light small text-muted text-center">
                    User ID: <strong>{{ $userId }}</strong> (Session Key)
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Panduan Koneksi</h5>
                    <ol class="ps-3 text-muted space-y-2">
                        <li class="mb-2">Buka WhatsApp di HP Anda.</li>
                        <li class="mb-2">Ketuk menu <strong>Titik Tiga</strong> (Android) atau <strong>Settings</strong>
                            (iPhone).</li>
                        <li class="mb-2">Pilih <strong>Linked Devices</strong> (Perangkat Tertaut).</li>
                        <li class="mb-2">Ketuk <strong>Link a Device</strong>.</li>
                        <li>Arahkan kamera ke QR Code yang muncul di layar ini.</li>
                    </ol>
                    <div class="alert alert-info small mt-4">
                        <i class="fas fa-info-circle me-1"></i> Pastikan server Node.js Skillance sudah berjalan di port
                        3000.
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        // Konfigurasi Koneksi ke Node.js
        const SERVER_URL = "http://localhost:3000"; // Ganti dengan IP VPS nanti kalau sudah online
        const socket = io(SERVER_URL);
        const myId = "{{ $userId }}"; // ID User Laravel

        // Element DOM
        const statusText = document.getElementById('status-text');
        const statusDesc = document.getElementById('status-desc');
        const qrArea = document.getElementById('qr-area');
        const qrContainer = document.getElementById('qrcode');
        const btnScan = document.getElementById('btn-scan');
        const btnLogout = document.getElementById('btn-logout');
        const spinner = document.getElementById('loading-spinner');

        // --- LOGIC SOCKET.IO ---

        // 1. Dengar Event: QR CODE DATANG
        socket.on('qr_code', (data) => {
            if (data.id == myId) {
                updateUI('scan');
                // Render QR String jadi Gambar
                qrContainer.innerHTML = ""; // Bersihkan QR lama
                new QRCode(qrContainer, {
                    text: data.qr,
                    width: 200,
                    height: 200
                });
            }
        });

        // 2. Dengar Event: SUDAH READY/CONNECT
        socket.on('ready', (data) => {
            if (data.id == myId) {
                updateUI('connected');
            }
        });

        // 3. Dengar Event: LOGOUT/DISCONNECT
        socket.on('disconnected', (data) => {
            if (data.id == myId) {
                updateUI('disconnected');
                alert('Koneksi WhatsApp Terputus!');
            }
        });

        // --- FUNGSI TOMBOL ---

        function startSession() {
            updateUI('loading');

            // Panggil API Node.js untuk nyalakan sesi
            fetch(`${SERVER_URL}/start-session`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_user: myId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    console.log(data.message);
                    if (data.status === 'connected') {
                        updateUI('connected');
                    }
                })
                .catch(err => {
                    alert("Gagal menghubungi Server WA. Pastikan Node.js jalan!");
                    updateUI('disconnected');
                });
        }

        function logoutSession() {
            if (confirm("Yakin ingin memutus koneksi WhatsApp?")) {
                // Disini bisa tambahkan endpoint logout di Node.js jika perlu
                // Untuk sekarang kita refresh halaman saja
                location.reload();
            }
        }

        // --- HELPER UI ---
        function updateUI(state) {
            if (state === 'loading') {
                spinner.style.display = 'block';
                statusText.innerText = "Menghubungkan...";
                statusText.className = "fw-bold text-warning";
                statusDesc.innerText = "Sedang menyiapkan sesi WhatsApp...";
                btnScan.disabled = true;
                qrArea.style.display = 'none !important';
            } else if (state === 'scan') {
                spinner.style.display = 'none';
                statusText.innerText = "Scan QR Code";
                statusText.className = "fw-bold text-primary";
                statusDesc.innerText = "Silakan scan kode di bawah ini secepatnya.";
                qrArea.style.setProperty('display', 'flex', 'important'); // Munculkan kotak QR
                btnScan.style.display = 'none';
            } else if (state === 'connected') {
                spinner.style.display = 'none';
                statusText.innerText = "Terhubung";
                statusText.className = "fw-bold text-success";
                statusDesc.innerText = "WhatsApp Gateway siap digunakan untuk mengirim notifikasi.";
                qrArea.style.setProperty('display', 'none', 'important');
                btnScan.style.display = 'none';
                btnLogout.style.display = 'block';
            } else { // disconnected
                spinner.style.display = 'none';
                statusText.innerText = "Disconnected";
                statusText.className = "fw-bold text-secondary";
                statusDesc.innerText = "Klik tombol di bawah untuk menghubungkan.";
                qrArea.style.setProperty('display', 'none', 'important');
                btnScan.style.display = 'block';
                btnScan.disabled = false;
                btnLogout.style.display = 'none';
            }
        }
    </script>
@endsection

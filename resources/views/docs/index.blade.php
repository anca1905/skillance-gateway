@extends('layouts.main')

@section('header', 'Dokumentasi API')

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-code me-2"></i>API Kirim Pesan</h5>
                    </div>
                    <div class="card-body">
                        <p>Gunakan endpoint ini untuk mengirim pesan WhatsApp dari aplikasi pihak ketiga (Laravel, Flutter,
                            Node.js, dll).</p>

                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-1"></i> Pastikan Device Anda statusnya <strong>Connected</strong>
                            sebelum mengirim pesan.
                        </div>

                        <h6 class="fw-bold mt-4">Endpoint URL</h6>
                        <div class="bg-dark text-white p-3 rounded d-flex justify-content-between align-items-center">
                            <code class="text-warning">POST http://localhost:3000/send-message</code>
                            <button class="btn btn-sm btn-outline-light" onclick="alert('URL Disalin!')">Copy</button>
                        </div>

                        <h6 class="fw-bold mt-4">Parameter Body (JSON)</h6>
                        <table class="table table-bordered small">
                            <thead class="bg-light">
                                <tr>
                                    <th>Parameter</th>
                                    <th>Wajib?</th>
                                    <th>Tipe</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>token</code></td>
                                    <td><span class="badge bg-danger">Ya</span></td>
                                    <td>String</td>
                                    <td>API Token dari menu <strong>Kelola Device</strong>.</td>
                                </tr>
                                <tr>
                                    <td><code>number</code></td>
                                    <td><span class="badge bg-danger">Ya</span></td>
                                    <td>String</td>
                                    <td>Nomor tujuan (Bisa 08xx atau 628xx).</td>
                                </tr>
                                <tr>
                                    <td><code>message</code></td>
                                    <td><span class="badge bg-danger">Ya</span></td>
                                    <td>String</td>
                                    <td>Isi pesan teks yang akan dikirim.</td>
                                </tr>
                            </tbody>
                        </table>

                        <h6 class="fw-bold mt-4">Contoh Request (PHP / Laravel)</h6>
                        <pre class="bg-light p-3 border rounded small">
use Illuminate\Support\Facades\Http;

$response = Http::post('http://localhost:3000/send-message', [
    'token'   => 'ganti_dengan_token_anda',
    'number'  => '081234567890',
    'message' => 'Halo, pesan ini dari Skillance Gateway!'
]);
</pre>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-primary text-white">
                    <div class="card-body">
                        <h5 class="fw-bold"><i class="fas fa-life-ring me-2"></i>Bantuan</h5>
                        <p class="small">Jika mengalami kendala integrasi, silakan hubungi tim teknis kami.</p>
                        <hr>
                        <small>Versi API: <strong>v1.0.0</strong></small><br>
                        <small>Status Server: <span class="badge bg-success">Online</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

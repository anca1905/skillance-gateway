<!DOCTYPE html>
<html lang="id">

<head>
    <title>Skillance Gateway Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand mb-0 h1">🚀 Skillance Gateway</span>
            <form action="/logout" method="POST" class="d-flex">
                @csrf <button class="btn btn-outline-danger btn-sm">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">Device Connection</div>
                    <div class="card-body text-center">
                        <div id="status" class="alert alert-warning">Menunggu Server...</div>

                        <div id="qrcode" class="d-flex justify-content-center my-3"></div>

                        <button onclick="initWA()" class="btn btn-primary w-100">Mulai / Refresh Sesi</button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">Integrasi API</div>
                    <div class="card-body">
                        <p>Gunakan endpoint ini di aplikasi Anda (Laravel/Flutter):</p>
                        <div class="bg-dark text-white p-3 rounded small mb-3">
                            POST http://localhost:3000/send-message
                        </div>
                        <p>Body JSON:</p>
                        <pre class="bg-light p-2 border rounded">
{
  "userId": "{{ Auth::id() }}",  <-- ID UNIK ANDA
  "number": "62812345678",
  "message": "Halo dari Skillance!"
}
                    </pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const socket = io("http://localhost:3000");
        const myId = "{{ Auth::id() }}";
        const qrContainer = document.getElementById("qrcode");
        const statusBox = document.getElementById("status");

        function initWA() {
            statusBox.className = "alert alert-info";
            statusBox.innerText = "Meminta QR Code...";

            fetch('http://localhost:3000/init', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    userId: myId
                })
            });
        }

        socket.on('qr', (data) => {
            if (data.userId == myId) {
                statusBox.className = "alert alert-warning";
                statusBox.innerText = "Silakan Scan QR Code di Bawah:";
                qrContainer.innerHTML = "";
                new QRCode(qrContainer, {
                    text: data.qr,
                    width: 200,
                    height: 200
                });
            }
        });

        socket.on('ready', (data) => {
            if (data.userId == myId) {
                statusBox.className = "alert alert-success";
                statusBox.innerText = "✅ WhatsApp Terhubung! API Siap Digunakan.";
                qrContainer.innerHTML = "";
            }
        });
    </script>

</body>

</html>

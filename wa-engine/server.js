const { Client, LocalAuth } = require('whatsapp-web.js');
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');
const fs = require('fs');
const mysql = require('mysql2/promise'); // Library Database
const axios = require('axios');

const app = express();
app.use(cors());
app.use(express.json());

app.get('/', (req, res) => {
    res.send('WA Engine is running');
});


const server = http.createServer(app);
const io = new Server(server, { cors: { origin: "*" } });

// KONEKSI DATABASE (Sesuaikan password/db kamu)
const dbConfig = {
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'db_skillance_gateway' 
};

const sessions = {};

// --- FUNGSI INIT SESSION (Sama seperti sebelumnya) ---
const initSession = (token) => {
    const client = new Client({
        authStrategy: new LocalAuth({ clientId: token }),
        puppeteer: { headless: true, args: ['--no-sandbox'] }
    });

    client.on('qr', (qr) => io.to(token).emit('qr', { token, qr }));
    client.on('ready', () => {
        io.to(token).emit('ready', { token });
        // Update status di DB jadi 'connected'
        updateDeviceStatus(token, 'connected');
    });

    // --- FITUR AUTO REPLY ---
    client.on('message', async (msg) => {
        if (msg.fromMe) return; // Jangan respon diri sendiri

        try {
            const connection = await mysql.createConnection(dbConfig);

            // 1. Ambil Device Info (Termasuk Webhook URL)
            const [devRows] = await connection.execute('SELECT id, webhook_url FROM devices WHERE api_token = ? LIMIT 1', [token]);
            if (devRows.length === 0) { await connection.end(); return; }

            const deviceId = devRows[0].id;
            const webhookUrl = devRows[0].webhook_url;
            const incomingMsg = msg.body.toLowerCase();
            const senderNumber = msg.from.replace('@c.us', '');

            // 2. CEK AUTO REPLY (PRIORITAS UTAMA)
            const [replies] = await connection.execute('SELECT * FROM autoreplies WHERE device_id = ?', [deviceId]);

            let replied = false;
            for (let reply of replies) {
                const dbKeyword = reply.keyword.toLowerCase();
                let isMatch = false;

                if (reply.search_type === 'exact' && incomingMsg === dbKeyword) isMatch = true;
                else if (reply.search_type === 'contains' && incomingMsg.includes(dbKeyword)) isMatch = true;

                if (isMatch) {
                    await client.sendMessage(msg.from, reply.response);
                    replied = true;
                    console.log(`🤖 Auto Reply ke ${senderNumber}: ${dbKeyword}`);
                    break;
                }
            }

            // 3. JIKA TIDAK ADA AUTO REPLY & ADA WEBHOOK -> LEMPAR KE WEBHOOK
            if (!replied && webhookUrl) {
                console.log(`🔗 Forwarding ke Webhook: ${webhookUrl}`);

                // Kirim data ke Aplikasi Laundry/Lainnya
                try {
                    const response = await axios.post(webhookUrl, {
                        device_token: token,
                        sender: senderNumber,
                        message: msg.body
                    });

                    // Jika Webhook membalas dengan JSON { "reply": "..." }
                    // Maka Bot akan meneruskan balasan itu ke User WA
                    if (response.data && response.data.reply) {
                        await client.sendMessage(msg.from, response.data.reply);
                        console.log(`🔗 Webhook Membalas: ${response.data.reply}`);
                    }
                } catch (webhookErr) {
                    console.error("❌ Webhook Error:", webhookErr.message);
                }
            }

            await connection.end();

        } catch (error) {
            console.error("Message Handler Error:", error);
        }
    });

    client.on('disconnected', () => updateDeviceStatus(token, 'disconnected'));

    client.initialize();
    sessions[token] = client;
}

// Helper: Update Status Device di DB
async function updateDeviceStatus(token, status) {
    try {
        const connection = await mysql.createConnection(dbConfig);
        await connection.execute('UPDATE devices SET status = ? WHERE api_token = ?', [status, token]);
        await connection.end();
    } catch (e) { console.error("DB Error:", e); }
}

io.on('connection', (socket) => {
    socket.on('join_room', (token) => socket.join(token));
});

// API INIT
app.post('/init', (req, res) => {
    const { token } = req.body;
    if (sessions[token]) return res.json({ status: true, message: 'Running' });
    initSession(token);
    res.json({ status: true, message: 'Initializing' });
});

// --- API KIRIM PESAN (DENGAN LOGIKA BISNIS) ---
app.post('/send-message', async (req, res) => {
    const { token, number, message } = req.body;

    // 1. CEK KONEKSI WA
    const client = sessions[token];
    if (!client) return res.status(401).json({ status: false, message: 'Device Belum Terhubung/Scan!' });

    try {
        // 2. CEK DATABASE (KUOTA & EXPIRED)
        const connection = await mysql.createConnection(dbConfig);
        const [rows] = await connection.execute('SELECT * FROM devices WHERE api_token = ? LIMIT 1', [token]);

        if (rows.length === 0) {
            await connection.end();
            return res.status(404).json({ status: false, message: 'Token Salah!' });
        }

        const device = rows[0];
        const today = new Date().toISOString().split('T')[0];

        // LOGIKA 1: CEK MASA AKTIF
        if (device.expired_date && device.expired_date < today) {
            await connection.end();
            return res.status(403).json({ status: false, message: 'Masa Aktif Habis. Silakan perpanjang (Rp 50rb).' });
        }

        // LOGIKA 2: CEK KUOTA
        if (device.quota <= 0) {
            await connection.end();
            return res.status(403).json({ status: false, message: 'Kuota Pesan Habis. Upgrade paket Anda.' });
        }

        // 3. KIRIM PESAN WA
        let formattedNumber = number.toString().replace(/\D/g, '');
        if (formattedNumber.startsWith('0')) formattedNumber = '62' + formattedNumber.substring(1);
        const chatId = formattedNumber + "@c.us";

        const isRegistered = await client.isRegisteredUser(chatId);
        if (!isRegistered) {
            await connection.end();
            return res.status(400).json({ status: false, message: 'Nomor tidak terdaftar WA' });
        }

        await client.sendMessage(chatId, message);

        // 4. POTONG KUOTA (-1)
        await connection.execute('UPDATE devices SET quota = quota - 1 WHERE id = ?', [device.id]);
        await connection.end();

        res.json({
            status: true,
            message: 'Terkirim',
            sisa_quota: device.quota - 1
        });

    } catch (e) {
        console.error(e);
        res.status(500).json({ status: false, error: e.message });
    }
});

// AUTO RESTORE
const SESSION_DIR = './.wwebjs_auth';
if (fs.existsSync(SESSION_DIR)) {
    fs.readdirSync(SESSION_DIR).forEach(file => {
        if (file.startsWith('session-')) {
            const token = file.replace('session-', '');
            initSession(token);
        }
    });
}

// HELPER: FUNGSI SLEEP (JEDA WAKTU)
const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));

// HELPER: FUNGSI RANDOM DELAY (Antara min dan max detik)
const randomDelay = (min, max) => Math.floor(Math.random() * (max - min + 1) + min) * 1000;

// ... helper sleep & randomDelay tetap ada di atas ...

app.post('/broadcast', async (req, res) => {
    const { token, targets, message } = req.body;

    const client = sessions[token];
    if (!client) return res.status(401).json({ status: false, message: 'Device Disconnected' });

    res.json({ status: true, message: 'Broadcast berjalan di background...' });

    // --- PROSES BACKGROUND ---
    (async () => {
        let connection;
        try {
            // 1. Buka Koneksi DB
            connection = await mysql.createConnection(dbConfig);

            // 2. Ambil ID Device berdasarkan Token (PENTING BUAT RELASI)
            const [rows] = await connection.execute('SELECT id FROM devices WHERE api_token = ? LIMIT 1', [token]);
            if (rows.length === 0) return; // Token ga valid, stop.
            const deviceId = rows[0].id;

            for (const number of targets) {
                // Format Nomor
                let formatted = number.toString().replace(/\D/g, '');
                if (formatted.startsWith('0')) formatted = '62' + formatted.substring(1);
                const chatId = formatted + "@c.us";

                let status = 'failed';
                let errorLog = null;

                try {
                    // Cek Register
                    const isRegistered = await client.isRegisteredUser(chatId);

                    if (isRegistered) {
                        await client.sendMessage(chatId, message);
                        status = 'success';
                        console.log(`✅ Terkirim: ${formatted}`);
                    } else {
                        status = 'failed';
                        errorLog = 'Nomor tidak terdaftar di WA';
                        console.log(`❌ Gagal: ${formatted} (Not Registered)`);
                    }

                } catch (err) {
                    status = 'failed';
                    errorLog = err.message;
                    console.log(`❌ Error: ${err.message}`);
                }

                // 3. SIMPAN LAPORAN KE DATABASE (INSERT)
                await connection.execute(
                    `INSERT INTO message_histories (device_id, number, message, status, error_log, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())`,
                    [deviceId, formatted, message, status, errorLog]
                );

                // Delay Anti-Banned
                const delay = randomDelay(3, 6);
                await sleep(delay);
            }

        } catch (e) {
            console.error("Broadcast System Error:", e);
        } finally {
            if (connection) await connection.end(); // Tutup koneksi DB biar hemat resource
        }
    })();
});

const PORT = process.env.PORT || 5000;

server.listen(PORT, () => {
    console.log(`🚀 Skillance Gateway running on port ${PORT}`);
});

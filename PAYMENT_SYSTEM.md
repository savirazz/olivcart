# Sistem Pembayaran Olvart - Dokumentasi

## 📌 Overview

Sistem pembayaran terintegrasi untuk menangani pembayaran rental dengan dua metode:
1. **Cash (Tunai)** - Pembayaran langsung saat penjemputan
2. **Payment Gateway (Midtrans)** - Pembayaran online dengan berbagai metode

## 🏗️ Arsitektur Sistem

```
User Flow:
Keranjang → Bayar (Select Payment) → Payment Status/Gateway → Admin Verify → Rental Approved
```

## 📁 File-File Sistem Pembayaran

### Core Files
- **`payment_helper.php`** - Helper functions untuk CRUD pembayaran
- **`payments.json`** - Database JSON untuk menyimpan data pembayaran

### User Pages
- **`bayar.php`** - Halaman pemilihan metode pembayaran
- **`payment_status.php`** - Halaman status pembayaran & receipt
- **`bayar_gateway.php`** - Halaman pembayaran melalui Midtrans

### Admin Pages
- **`kelola_pembayaran.php`** - Dashboard admin untuk verifikasi pembayaran

### Modified Files
- **`keranjang.php`** - Redirect checkout ke bayar.php

## 🔄 Payment Status Flow

### Cash Payment
```
1. User pilih "Cash" → Status: pending
2. User menerima kode pembayaran & detail rental
3. Admin verifikasi pembayaran saat penjemputan → Status: paid
4. Rental otomatis diupdate sebagai dibayar
```

### Gateway Payment (Midtrans)
```
1. User pilih "Gateway" → Status: unpaid
2. Redirect ke halaman Midtrans Snap
3. User melakukan pembayaran → Callback dari Midtrans → Status: paid
4. Rental otomatis diupdate sebagai dibayar
```

## 💾 Data Structure

### Payment Object
```php
[
    'id' => 1,                           // Payment ID
    'user_id' => 5,                      // User ID
    'payment_code' => 'PAY-ABC1234D',   // Unique payment code
    'order_id' => 'ORDER-20240101...',  // For gateway method
    'rental_ids' => [1, 2, 3],          // Linked rental IDs
    'amount' => 500000.00,              // Total amount in IDR
    'method' => 'cash|gateway',         // Payment method
    'status' => 'pending|unpaid|paid|cancelled|expired',
    'notes' => 'Optional notes',        // Notes
    'gateway_response' => null,         // Midtrans response (if any)
    'paid_at' => '2024-01-15 10:30:00',
    'verified_by' => 'Admin Name',      // Who verified
    'verified_at' => '2024-01-15 10:35:00',
    'created_at' => '2024-01-15 10:00:00',
    'updated_at' => '2024-01-15 10:35:00'
]
```

### Rental Updates
Setiap Rental sekarang memiliki field:
- `payment_status` - Status pembayaran untuk rental tersebut

## 🔑 Helper Functions di payment_helper.php

### CRUD Operations
- `getPayments()` - Get semua pembayaran
- `savePayments($payments)` - Save pembayaran ke file
- `findPaymentById($id)` - Get pembayaran by ID
- `getNextPaymentId()` - Generate ID baru
- `getPaymentsByUserId($userId)` - Get pembayaran user tertentu

### Business Logic
- `createPayment($userId, $amount, $rentalIds, $method, $notes)` - Buat pembayaran baru
- `updatePaymentStatus($id, $status, $verifiedBy)` - Update status pembayaran
- `calculateTotalAmountForRentals($rentalIds)` - Hitung total dari rental
- `markRentalsAsPaid($rentalIds)` - Mark rentals sebagai dibayar

### Display Helpers
- `getPaymentStatusLabel($status)` - Label untuk status
- `getPaymentStatusColor($status)` - Warna untuk status
- `getPaymentMethodLabel($method)` - Label untuk metode

## 🔐 Security Features

### Payment Gateway (Midtrans)
- ✅ HTTPS/SSL encryption
- ✅ PCI DSS compliance
- ✅ Server-side validation
- ✅ Unique payment codes
- ✅ Transaction verification

### Cash Payment
- ✅ Verification by admin
- ✅ Payment code tracking
- ✅ Audit trail dengan verified_by & verified_at

## 📊 Admin Payment Management

### Features
- Filter by payment method (Cash / Gateway)
- Filter by status (Pending / Unpaid / Paid / Cancelled)
- Verify cash payments
- View payment details
- Print receipts
- Audit trail

### Verification Process
1. Admin lihat list pembayaran dengan status "pending" atau "unpaid"
2. Klik tombol "Verifikasi" untuk cash payment
3. System update status ke "paid" dan mark rentals sebagai dibayar
4. Send receipt ke user

## 💳 Midtrans Integration

### Configuration
File: `bayar_gateway.php`

```php
define('MIDTRANS_SERVER_KEY', '...');
define('MIDTRANS_CLIENT_KEY', '...');
define('MIDTRANS_API_URL', 'https://api.sandbox.midtrans.com/v2');
define('MIDTRANS_SNAP_URL', 'https://snap.sandbox.midtrans.com/snap.js');
```

### Payment Methods Available (Midtrans)
- ✓ Kartu Kredit (Visa, Mastercard, JCB)
- ✓ Debit (Visa, Mastercard)
- ✓ E-wallet (GCash, OVO, Dana, LinkAja)
- ✓ Transfer Bank (BNI, BCA, Mandiri, BRI)
- ✓ COD (Cash on Delivery)

### Webhook Integration
Midtrans akan send callback ke sistem ketika:
- Payment berhasil
- Payment gagal
- Payment pending

## 🧪 Testing

### Demo Mode
File `bayar_gateway.php` menyediakan mode demo untuk testing tanpa real Midtrans credentials.

Untuk production, replace dengan real server key & client key dari Midtrans dashboard.

### Test Credentials (Sandbox)
```
Email: admin@olvart.test
Password: admin123
```

Test Payment:
1. Login sebagai user
2. Add items ke cart
3. Checkout → Pilih "Gateway"
4. Klik "Lanjutkan ke Midtrans"
5. Simulasi pembayaran berhasil
6. Cek payment status page

## 📈 Reports & Analytics

### Available Metrics
- Total pembayaran per periode
- Breakdown by payment method
- Breakdown by status
- Revenue trends
- Payment success rate

(Dapat diintegrasikan ke dashboard admin)

## 🐛 Troubleshooting

### Issue: "Pembayaran tidak ditemukan"
- Pastikan user yang login adalah user yang membuat pembayaran
- Check payment ID di URL

### Issue: Admin tidak bisa verifikasi
- Pastikan role admin/staff sudah benar
- Check permissions di `canApproveRentals()`

### Issue: Gateway tidak berfungsi
- Pastikan Midtrans credentials benar di `bayar_gateway.php`
- Check internet connection untuk callback

## 📝 Next Steps

### Future Enhancements
1. **Payment Reminders** - Email reminder untuk unpaid payments
2. **Auto-cancellation** - Batalkan payment setelah timeout
3. **Refund System** - Handle refund untuk cancelled rentals
4. **Payment Analytics** - Dashboard untuk revenue tracking
5. **Multiple Currencies** - Support untuk currency selain IDR
6. **Invoice Generation** - Buat PDF invoice otomatis
7. **Reconciliation Report** - Laporan rekonsiliasi bank
8. **Payment Plans** - Cicilan untuk pembayaran besar

## 👨‍💼 Admin Checklist

- [ ] Configure Midtrans credentials
- [ ] Setup webhook URL di Midtrans dashboard
- [ ] Test cash payment flow
- [ ] Test gateway payment flow
- [ ] Setup email notifications (opsional)
- [ ] Train staff untuk payment verification
- [ ] Setup payment reconciliation schedule
- [ ] Monitor payment success rate

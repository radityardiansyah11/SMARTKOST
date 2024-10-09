<script>
    document.addEventListener('DOMContentLoaded', function () {
        const paymentForm = document.querySelector('form');
        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        const modalBody = document.getElementById('modalBody');
        const totalPrice = <?php echo json_encode($total_harga); ?>; // Passing total price from PHP to JS

        paymentForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            // Ambil nilai dari input
            const namaPenyewa = document.getElementById('nama').value;
            const emailPenyewa = document.getElementById('emailPenyewa').value;
            const method = document.getElementById('metodePembayaran').value;

            let modalContent = '';

            if (method === 'transfer') {
                modalContent = `
<h5>Detail Transfer Bank</h5>
<form id="transferForm">
    <div class="row">
        <!-- Kolom Kiri -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="senderName" class="form-label">Nama Pengirim</label>
                <input type="text" class="form-control" id="senderName" placeholder="${namaPenyewa}">
            </div>
            <div class="mb-3">
                <label for="senderEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="senderEmail" placeholder="${emailPenyewa}">
            </div>
            <div class="mb-3">
                <label for="transferDate" class="form-label">Lama Sewa</label>
                <div class="d-flex justify-content-between mb-3">
                    <div class="date-box">
                        <strong>Mulai:</strong> ${<?php echo json_encode($mulai_sewa); ?>}
                    </div>
                    <div class="date-box">
                        <strong>Selesai:</strong> ${<?php echo json_encode($selesai_sewa); ?>}
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="bankName" class="form-label">Bank</label>
                <select class="form-select" id="bankName">
                    <option value="BCA">BCA</option>
                    <option value="Mandiri">Mandiri</option>
                    <option value="BRI">BRI</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="accountNumber" class="form-label">Nomor Rekening</label>
                <input type="text" class="form-control" id="accountNumber" placeholder="Masukkan Nomor Rekening">
            </div>
            <div class="mb-3">
                <label for="transferAmount" class="form-label">Jumlah Transfer</label>
                <input type="text" class="form-control" id="transferAmount" value="Rp ${totalPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');}" style="background-color: white;">
            </div>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-primary w-50">Konfirmasi Transfer</button>
        </div>
</form>
`;
            } else if (method === 'ewallet') {
                modalContent = `
<h5>Detail E-Wallet</h5>
<form id="ewalletForm">
    <div class="row">
        <!-- Kolom Kiri -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="ewalletSenderName" class="form-label">Nama Pengirim</label>
                <input type="text" class="form-control" id="ewalletSenderName" placeholder="${namaPenyewa}">
            </div>
            <div class="mb-3">
                <label for="ewalletEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="ewalletEmail" placeholder="${emailPenyewa}">
            </div>
            <div class="mb-3">
                <label for="ewalletPaymentDate" class="form-label">Lama Sewa</label>
                <div class="d-flex justify-content-between mb-3">
                    <div class="date-box">
                        <strong>Mulai:</strong> ${<?php echo json_encode($mulai_sewa); ?>}
                    </div>
                    <div class="date-box">
                        <strong>Selesai:</strong> ${<?php echo json_encode($selesai_sewa); ?>}
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="Platform" class="form-label">Platform</label>
                <select class="form-select" id="Platform">
                    <option value="ovo" data-icon="fas fa-wallet">OVO</option>
                    <option value="gopay" data-icon="fab fa-google-wallet">GoPay</option>
                    <option value="dana" data-icon="fas fa-mobile-alt">Dana</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="ewalletAccountNumber" class="form-label">Nomor</label>
                <input type="text" class="form-control" id="ewalletAccountNumber" placeholder="Masukkan Nomor Rekening">
            </div>
            <div class="mb-3">
                <label for="ewalletTransferAmount" class="form-label">Jumlah Transfer</label>
                <input type="text" class="form-control" id="ewalletTransferAmount" value="Rp ${totalPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');}" style="background-color: white;">
            </div>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-primary w-50">Konfirmasi Transfer</button>
        </div>
</form>
`;
            } else if (method === 'cod') {
                modalContent = `
<h5>Detail COD</h5>
<form id="codForm">
    <div class="row">
        <!-- Kolom Kiri -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="codSenderName" class="form-label">Nama Pengirim</label>
                <input type="text" class="form-control" id="codSenderName" placeholder="${namaPenyewa}">
            </div>
            <div class="mb-3">
                <label for="codEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="codEmail" placeholder="${emailPenyewa}">
            </div>
            <div class="mb-3">
                <label for="codPaymentDate" class="form-label">Lama Sewa</label>
                <div class="d-flex justify-content-between mb-3">
                    <div class="date-box">
                        <strong>Mulai:</strong> ${<?php echo json_encode($mulai_sewa); ?>}
                    </div>
                    <div class="date-box">
                        <strong>Selesai:</strong> ${<?php echo json_encode($selesai_sewa); ?>}
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="codTransferAmount" class="form-label">Jumlah Transfer</label>
                <input type="text" class="form-control" id="codTransferAmount" value="Rp ${totalPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');}" style="background-color: white;">
            </div>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-primary w-50">Konfirmasi COD</button>
        </div>
</form>
`;
            }

            modalBody.innerHTML = modalContent; // Update modal body content
            paymentModal.show(); // Show the payment modal
        });
    });
</script>
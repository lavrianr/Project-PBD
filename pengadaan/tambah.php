<?php
$judul = 'Buat Pengadaan Baru';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_id = $_POST['vendor_idvendor'];
    $user_id   = 1;
    $idbarang  = $_POST['idbarang'] ?? [];
    $jumlah    = $_POST['jumlah'] ?? [];
    $harga     = $_POST['harga_satuan'] ?? [];

    if (!empty($idbarang) && !empty($vendor_id)) {
        $subtotal = 0;
        for ($i = 0; $i < count($idbarang); $i++) {
            $subtotal += ((int)$jumlah[$i] * (int)$harga[$i]);
        }
        $ppn = $subtotal * 0.11;
        $total = $subtotal + $ppn;

        $stmt = $pdo->prepare("INSERT INTO pengadaan (timestamp, user_iduser, status, vendor_idvendor, subtotal_nilai, ppn, total_nilai) VALUES (NOW(), ?, 'A', ?, ?, ?, ?)");
        $stmt->execute([$user_id, $vendor_id, $subtotal, $ppn, $total]);
        
        $idpengadaan = $pdo->lastInsertId();

        $stmt_detail = $pdo->prepare("INSERT INTO detail_pengadaan (harga_satuan, jumlah, sub_total, idbarang, idpengadaan) VALUES (?, ?, ?, ?, ?)");
        for ($i = 0; $i < count($idbarang); $i++) {
            $sub = (int)$jumlah[$i] * (int)$harga[$i];
            $stmt_detail->execute([(int)$harga[$i], (int)$jumlah[$i], $sub, (int)$idbarang[$i], $idpengadaan]);
        }

        header("Location: index.php");
        exit();
    } else {
        $pesan = 'Pilih vendor dan minimal 1 barang!';
    }
}

$vendors = $pdo->query("SELECT * FROM vendor ORDER BY nama_vendor ASC")->fetchAll();
$barang  = $pdo->query("SELECT * FROM barang ORDER BY nama ASC")->fetchAll();
?>

<?php if ($pesan): ?>
    <p style="color:red;"><?= htmlspecialchars($pesan) ?></p>
<?php endif; ?>

<div class="dashboard-card">
    <form method="POST">
        <p>Vendor:<br>
        <select name="vendor_idvendor" required>
            <option value="">-- Pilih Vendor --</option>
            <?php foreach ($vendors as $v): ?>
                <option value="<?= $v['idvendor'] ?>">
                    <?= htmlspecialchars($v['nama_vendor']) ?>
                </option>
            <?php endforeach; ?>
        </select></p>

        <h3>Barang yang Dipesan</h3>
        <table id="tabel-barang" style="width:100%;">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan (Rp)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="idbarang[]" onchange="isiHargaOtomatis(this)" required>
                            <option value="" data-harga="0">-- Pilih Barang --</option>
                            <?php foreach ($barang as $b): ?>
                                <option value="<?= $b['idbarang'] ?>" data-harga="<?= $b['harga'] ?>">
                                    <?= htmlspecialchars($b['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="number" name="jumlah[]" min="1" value="1" required></td>
                    <td><input type="number" name="harga_satuan[]" min="0" value="0" readonly required style="background-color: #f0f0f0;"></td>
                    <td><button type="button" onclick="hapusBaris(this)">Hapus</button></td>
                </tr>
            </tbody>
        </table>

        <br>
        <button type="button" onclick="tambahBaris()">+ Tambah Baris Barang</button>
        <br><br>
        <button type="submit">Simpan Pengadaan</button>
        <a href="index.php" class="btn" style="background:#fff; color:#000; margin-left:10px;">Batal</a>
    </form>
</div>

<script>
function isiHargaOtomatis(selectEl) {
    let optionTerpilih = selectEl.options[selectEl.selectedIndex];
    let harga = optionTerpilih.getAttribute('data-harga') || 0;
    let baris = selectEl.closest('tr');
    baris.querySelector('input[name="harga_satuan[]"]').value = harga;
}

function tambahBaris() {
    let tbody = document.querySelector("#tabel-barang tbody");
    let barisBaru = tbody.children[0].cloneNode(true);
    
    barisBaru.querySelector('select').selectedIndex = 0;
    barisBaru.querySelector('input[name="jumlah[]"]').value = 1;
    barisBaru.querySelector('input[name="harga_satuan[]"]').value = 0;
    
    tbody.appendChild(barisBaru);
}

function hapusBaris(btn) {
    let tbody = document.querySelector("#tabel-barang tbody");
    if (tbody.children.length > 1) {
        btn.closest("tr").remove();
    } else {
        alert("Minimal harus ada 1 item barang!");
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
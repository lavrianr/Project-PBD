<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Proyek PBD</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
   <div class="container" style="max-width: 700px;">
        <div style="background: #fffffff; padding: 30px; border-radius: 16px; box-shadow: 0 10px 30px rgba(90, 100, 85, 0.08); border: 1px solid #d5ded0; margin-bottom: 25px; text-align: center;">
            <h2 style="color: #8c5a5e; margin-bottom: 8px; font-size: 26px;"> Sistem Informasi Penjualan & Persediaan</h2>
            <p style="color: #6c757d; font-size: 14px;">Selamat datang di Aplikasi</p>
        </div>

        <div style="background: #ffffff; padding: 30px; border-radius: 16px; box-shadow: 0 10px 30px rgba(90, 100, 85, 0.08); border: 1px solid #d5ded0;">

            <h3 style="color: #4a5247; font-size: 15px; font-weight: 700; margin-bottom: 15px; letter-spacing: 0.05em;">Master Data</h3>
            <ul style="list-style: none; padding: 0; margin-bottom: 25px;">
                <li style="margin-bottom: 8px;">
                    <a href="satuan/index.php" style="display: block; padding: 12px 16px; background: #faf4f4; color: #8c5a5e; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 600; border: 1px solid #f0dede; transition: all 0.2s;">&bull; Kelola Data Satuan</a>
                </li>
            </ul>

            <h3 style="color #4a5247; font-size: 15px; font-weight: 700; margin-bottom: 15px; letter=spacing: 0.05em;">Laporan View Master</h3>
            <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 10px;">
                <li><a href="laporan/view_satuan.php" style="display: block; padding: 12px 16px; border: #f7faf5; color: #4a5247; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 500; border: 1px solid #edf2eb; transition: all 0.2s;">&bull; Laporan Data Satuan</a></li>
                <li><a href="laporan/view_barang.php" style="display: block; padding: 12px 16px; border: #f7faf5; color: #4a5247; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 500; border: 1px solid #edf2eb; transition: all 0.2s;">&bull; Laporan Data Barang</a></li>
                <li><a href="laporan/view_vendor.php" style="display: block; padding: 12px 16px; border: #f7faf5; color: #4a5247; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 500; border: 1px solid #edf2eb; transition: all 0.2s;">&bull; Laporan Data Vendor</a></li>
                <li><a href="laporan/view_user.php" style="display: block; padding: 12px 16px; border: #f7faf5; color: #4a5247; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 500; border: 1px solid #edf2eb; transition: all 0.2s;">&bull; Laporan Data User</a></li>
                <li><a href="laporan/view_role.php" style="display: block; padding: 12px 16px; border: #f7faf5; color: #4a5247; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 500; border: 1px solid #edf2eb; transition: all 0.2s;">&bull; Laporan Data Role</a></li>
                <li><a href="laporan/view_margin.php" style="display: block; padding: 12px 16px; border: #f7faf5; color: #4a5247; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 500; border: 1px solid #edf2eb; transition: all 0.2s;">&bull; Laporan Data Margin</a></li>
            </ul>

        </div>

    </div>
</body>
</html>
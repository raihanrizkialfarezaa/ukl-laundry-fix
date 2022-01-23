
<?php
require 'functions.php';
$status = ['baru','proses','selesai','diambil'];
$query = "SELECT transaksi.*,member.nama_member , detail_transaksi.*,outlet.nama_outlet,paket.nama_paket, paket.harga FROM transaksi INNER JOIN member ON member.id_member = transaksi.member_id INNER JOIN detail_transaksi ON detail_transaksi.transaksi_id = transaksi.id_transaksi INNER JOIN outlet ON outlet.id_outlet = transaksi.outlet_id INNER JOIN paket ON paket.outlet_id = transaksi.outlet_id  WHERE transaksi.id_transaksi=".$_GET['id'];
$data = ambilsatubaris($conn,$query);

if(isset($_POST['btn-simpan'])){
    $status     = $_POST['status'];
    $qty     = $_POST['qty'];
    $diskon = $data['diskon'];
    $pajak = $data['pajak'];
    $biaya_tambahan = $data['biaya_tambahan'];
    if ($biaya_tambahan != 0) {
        $total_harga = $data['harga'] * $qty + $biaya_tambahan;
    } elseif($diskon != 0) {
        $total = $data['harga'] * $qty;
        $diskon = ($diskon/100) * $total;
        $total_harga = $total - $diskon;
    } elseif($pajak != 0) {
        $total = $data['harga'] * $qty;
        $pajak = ($pajak/100) * $total;
        $total_harga = $total + $pajak;
    } elseif($biaya_tambahan != 0 AND $diskon != 0) {
        $total = $data['harga'] * $qty;
        $diskon = ($diskon/100) * $total;
        $total_harga = ($total - $diskon) + $biaya_tambahan;
    } elseif($biaya_tambahan != 0 AND $pajak != 0) {
        $total = $data['harga'] * $qty;
        $pajak = ($pajak/100) * $total;
        $total_harga = $total + $pajak + $biaya_tambahan;
    } elseif($diskon != 0 AND $pajak != 0) {
        $total = $data['harga'] * $qty;
        $diskon = ($diskon/100) * $total;
        $pajak = ($pajak/100) * $total;
        $total_harga = ($total - $diskon) + $pajak;
    } elseif($diskon != 0 AND $pajak != 0 AND $biaya_tambahan != 0) {
        $total = $data['harga'] * $qty;
        $diskon = ($diskon/100) * $total;
        $pajak = ($pajak/100) * $total;
        $total_harga = ($total - $diskon) + $pajak + $biaya_tambahan;
    } else {
        $total_harga = $data['harga'] * qty;
    }
    if ($data['status_bayar'] == 'dibayar') {
        $query = "UPDATE transaksi SET status = '$status' WHERE id_transaksi = " . $_GET['id'];
    } else {
        $query = "UPDATE transaksi, detail_transaksi SET transaksi.status = '$status', detail_transaksi.qty = '$qty', detail_transaksi.total_harga = '$total_harga' WHERE detail_transaksi.transaksi_id=transaksi.id_transaksi AND transaksi.id_transaksi = " . $_GET['id'];
    }
    
    $execute = bisa($conn,$query);
    if($execute == 1){
        $success = 'true';
        $title = 'Berhasil';
        $message = 'Berhasil mengubah status transaksi';
        $type = 'success';
        header('location: transaksi.php?crud='.$success.'&msg='.$message.'&type='.$type.'&title='.$title);
    }else{
        echo "Gagal Tambah Data";
    }
}

require'layout_header.php';

?> 
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Data Master Transaksi</h4> </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="outlet.php">Transaksi</a></li>
                <li><a href="#">Tambah Transaksi</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
            <div class="white-box">
                <div class="row">
                    <div class="col-md-6">
                          <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary box-title"><i class="fa fa-arrow-left fa-fw"></i> Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
            <div class="white-box">
                <form method="post" action="">
                <div class="form-group">
                    <label>Kode Invoice</label>
                    <input type="text" name="kode_invoice" class="form-control" readonly="" value="<?= $data['kode_invoice'] ?>">
                </div>
                <div class="form-group">
                    <label>Outlet</label>
                    <input type="text" name="username" class="form-control" readonly="" value="<?= $data['nama_outlet'] ?>">
                </div>
                <div class="form-group">
                    <label>Pelanggan</label>
                    <input type="text" name="password" class="form-control" readonly="" value="<?= $data['nama_member'] ?>"> 
                </div>
                <div class="form-group">
                    <label>Jenis Paket</label>
                    <input type="text" name="password" class="form-control" readonly="" value="<?= $data['nama_paket'] ?>"> 
                </div>
                <?php if($data['status_bayar'] == 'dibayar'): ?>
                <div class="form-group">
                    <label>Jumlah</label>
                    <input readonly="" type="text" name="qty" class="form-control" value="<?= $data['qty'] ?>"> 
                </div>
                <div class="form-group">
                    <label>Kembalian</label>
                    <input readonly="" type="text" name="qty" class="form-control" value="<?= $data['total_bayar'] - $data['total_harga'] ?>"> 
                </div>
                <?php if($diskon != 0): ?>
                <div class="form-group">
                    <label>Diskon</label>
                    <input readonly="" type="text" name="qty" class="form-control" value="<?= $diskon ?>"> 
                </div>
                <?php else: ?>
                <div class="form-group">
                    <label>Diskon</label>
                    <input readonly="" type="text" name="qty" class="form-control" value="0"> 
                </div>
                <?php endif; ?>
                <?php else: ?>
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="qty" class="form-control" value="<?= $data['qty'] ?>"> 
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <label>Total Harga</label>
                    <input readonly=""   type="text" name="total_harga" class="form-control" value="<?= $data['total_harga'] ?>"> 
                </div>
                <?php if ($data['total_bayar'] > 0 ): ?>
                    <div class="form-group">
                        <label>Total Bayar</label>
                        <input readonly=""   type="text" name="total_bayar" class="form-control" value="<?= $data['total_bayar'] ?>"> 
                    </div>
                    <div class="form-group">
                        <label>Di Bayar Pada Tanggal </label>
                        <input readonly=""   type="text" name="biaya_tambahan" class="form-control" value="<?= $data['tgl_pembayaran'] ?>"> 
                    </div>
                <?php else: ?>
                    <div class="form-group">
                    <label>Total Bayar</label>
                    <input readonly=""   type="text" name="biaya_tambahan" class="form-control" value="Belum Melakukan Pembayaran"> 
                </div>
                <div class="form-group">
                    <label>Batas Waktu Pembayaran</label>
                    <input readonly=""   type="text" name="biaya_tambahan" class="form-control" value="<?= $data['batas_waktu'] ?>"> 
                </div>
                <?php  endif;?>
                <?php if($data['status_bayar'] == 'dibayar'): ?>
                    <div class="form-group">
                        <label>Status Transaksi</label>
                        <select name="status" class="form-control">
                            <?php foreach ($status as $key): ?>
                            <?php if ($key == $data['status']): ?>
                            <option value="<?= $key ?>" selected><?= $key ?></option>
                            <?php endif ?>
                            <option value="<?= $key ?>"><?= $key ?></option>
                            <?php endforeach ?>
                        </select>
                        <small>Klik Tombol Ubah Untuk Menyimpan Perubahan Transaksi</small>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label>Status Transaksi</label>
                        <select name="status" class="form-control">
                            <?php foreach ($status as $key): ?>
                            <?php if ($key == $data['status']): ?>
                            <option value="<?= $key ?>" selected><?= $key ?></option>
                            <?php endif ?>
                            <?php endforeach ?>
                            <option value="baru">baru</option>
                            <option value="proses">proses</option>
                            <option value="selesai">selesai</option>
                        </select>
                        <small>Klik Tombol Ubah Untuk Menyimpan Perubahan Transaksi</small>
                    </div>
                <?php  endif;?>
                <div class="text-right">
                    <button type="submit" name="btn-simpan" class="btn btn-primary">Ubah</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require'layout_footer.php';
?> 
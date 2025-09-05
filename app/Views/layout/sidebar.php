<!-- <div class="sidebar" data-active-color="rose" data-background-color="black" data-image="<?=base_url()?>assets/img/sidebar-1.jpg"> -->
<div class="sidebar" data-active-color="rose" style="background-color: #7F9192 !important;" data-image="<?=base_url()?>assets/img/sidebar-1.jpg"> 
    <div class="logo">
        <a href="#" class="simple-text">
            Korry Studio
        </a>
    </div>
    <div class="logo logo-mini">
        <a href="#" class="simple-text">
            KS
        </a>
    </div>
    <div class="sidebar-wrapper">
        <div class="user">
            <div class="info">
                <a data-toggle="collapse" href="#collapseExample" class="collapsed">
                    <?=$_SESSION["logged_status"]["nama"]?>
                    <b class="caret"></b>
                </a>
                <div class="collapse" id="collapseExample">
                    <ul class="nav">
                        <li>
                            <a href="<?=base_url()?>admin/pengguna/ubah/<?=base64_encode($_SESSION["logged_status"]["username"])?>">Ganti Password</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
            <ul class="nav">
            <?php if (@$_SESSION["logged_status"]["role"]=="Owner"){?>
                <li class="<?=@$mn_dash?>">
                    <a href="<?=base_url()?>dashboard">
                        <i class="material-icons">dashboard</i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="<?=@$mn_setting?>">
                    <a data-toggle="collapse" href="#settings" aria-expanded="<?php echo (@$mn_setting=="active")?"true":"false" ?>">
                        <i class="material-icons">settings</i>
                        <p>Setup
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="<?=@$colset?>" id="settings">
                        <ul class="nav">
                            <li class="<?=@$side1?>">
                                <a href="<?=base_url()?>admin/pengguna">Pengguna</a>
                            </li>
                            <li class="<?=@$side2?>">
                                <a href="<?=base_url()?>admin/store">Store</a>
                            </li>
                            <li class="<?=@$side3?>">
                                <a href="<?=base_url()?>admin/brand">Brand</a>
                            </li>
                            <li class="<?=@$side4?>">
                                <a href="<?=base_url()?>admin/kategori">Kategori</a>
                            </li>
                            <li class="<?=@$side5?>">
                                <a href="<?=base_url()?>admin/size">Size</a>
                            </li>
                            <li class="<?=@$side6?>">
                                <a href="<?=base_url()?>admin/bahanbaku">Bahan Baku</a>
                            </li>
                            <li class="<?=@$side7?>">
                                <a href="<?=base_url()?>admin/fabric">Fabric</a>
                            </li>
                            <li class="<?=@$side8?>">
                                <a href="<?=base_url()?>admin/warna">Warna</a>
                            </li>
                            <li class="<?=@$side9?>">
                                <a href="<?=base_url()?>admin/pengeluaran">Pengeluaran</a>
                            </li>
                            <li class="<?=@$side10?>">
                                <a href="<?=base_url()?>admin/metodebayar">Metode Bayar</a>
                            </li>
                            <li class="<?=@$side11?>">
                                <a href="<?=base_url()?>admin/biayaproduksi">Biaya Produksi</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="<?=@$mn_master?>">
                    <a data-toggle="collapse" href="#master" aria-expanded="<?php echo (@$mn_master=="active")?"true":"false" ?>">
                        <i class="material-icons">library_add</i>
                        <p>Master Data
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="<?=@$colmas?>" id="master">
                        <ul class="nav">
                            <li class="<?=@$side12?>">
                                <a href="<?=base_url()?>admin/assignstaff">Staff</a>
                            </li>
                            <li class="<?=@$side13?>">
                                <a href="<?=base_url()?>admin/produk">Produk</a>
                            </li>
                            <li class="<?=@$side14?>">
                                <a href="<?=base_url()?>admin/produksize">Produk Size</a>
                            </li>
                            <li class="<?=@$side15?>">
                                <a href="<?=base_url()?>admin/stok">Stok</a>
                            </li>
                            <li class="<?=@$side16?>">
                                <a href="<?=base_url()?>admin/stokbahanbaku">Stok Bahan Baku</a>
                            </li>
                            <li class="<?=@$side17?>">
                                <a href="<?=base_url()?>admin/vendorproduksi">Vendor</a>
                            </li>
                            <li class="<?=@$side18?>">
                                <a href="<?=base_url()?>admin/partner">Partner</a>
                            </li>
                            <li class="<?=@$side19?>">
                                <a href="<?=base_url()?>admin/wholesaler">Wholesaler</a>
                            </li>
                        </ul>
                    </div>
                </li>
    			<li class="<?=@$mn_produksi?>">
    				<a href="<?=base_url()?>produksi">
    					<i class="material-icons">business</i>
    					<p>Produksi</p>
    				</a>
    			</li>
                <li class="<?=@$mn_konsinyasi?>">
                    <a data-toggle="collapse" href="#konsinyasi" aria-expanded="<?php echo (@$mn_konsinyasi=="active")?"true":"false" ?>">
                        <i class="material-icons">group</i>
                        <p>Konsinyasi
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="<?=@$collap?>" id="konsinyasi">
                        <ul class="nav">
                            <li class="<?=@$side20?>">
                                <a href="<?=base_url()?>admin/konsinyasi/do">Delivery Order</a>
                            </li>
                            <li class="<?=@$side21?>">
                                <a href="<?=base_url()?>admin/konsinyasi/nota">Nota</a>
                            </li>
                            <li class="<?=@$side22?>">
                                <a href="<?=base_url()?>admin/konsinyasi/retur">Retur</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="<?=@$mn_wholesale?>">
                    <a data-toggle="collapse" href="#wholesale" aria-expanded="<?php echo (@$mn_wholesale=="active")?"true":"false" ?>">
                        <i class="material-icons">shopping_basket</i>
                        <p>Wholesale
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="<?=@$collap?>" id="wholesale">
                        <ul class="nav">
                            <li class="<?=@$side23?>">
                                <a href="<?=base_url()?>admin/wholesale/order">Order</a>
                            </li>
                            <li class="<?=@$side24?>">
                                <a href="<?=base_url()?>admin/wholesale/cicilan">Cicilan</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="<?=@$mn_laporan?>">
                    <a data-toggle="collapse" href="#laporan" aria-expanded="<?php echo (@$mn_laporan=="active")?"true":"false" ?>">
                        <i class="material-icons">settings</i>
                        <p>Laporan
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="<?=@$collap?>" id="laporan">
                        <ul class="nav">
                            <li class="<?=@$side25?>">
                                <a href="<?=base_url()?>admin/laporan/mutasi">Persediaan Global</a>
                            </li>
                            <li class="<?=@$side26?>">
                                <a href="<?=base_url()?>admin/laporan/mutasidetail">Persediaan Detail</a>
                            </li>
                            <li class="<?=@$side27?>">
                                <a href="<?=base_url()?>admin/laporan/penjualan">Penjualan</a>
                            </li>
                            <li class="<?=@$side28?>">
                                <a href="<?=base_url()?>admin/laporan/penjualandetail">Penjualan Detail</a>
                            </li>
                            <li class="<?=@$side29?>">
                                <a href="<?=base_url()?>admin/laporan/brand">Penjualan Brand</a>
                            </li>
                            <li class="<?=@$side30?>">
                                <a href="<?=base_url()?>admin/laporan/barang">Drop In/Drop Out</a>
                            </li>
                            <li class="<?=@$side31?>">
                                <a href="<?=base_url()?>admin/laporan/nontunai">Non Tunai</a>
                            </li>
                            <li class="<?=@$side32?>">
                                <a href="<?=base_url()?>admin/laporan/request">Permintaan</a>
                            </li>
                            <li class="<?=@$side33?>">
                                <a href="<?=base_url()?>admin/laporan/retur">Retur Konsumen</a>
                            </li>
                            <li class="<?=@$side34?>">
                                <a href="<?=base_url()?>admin/laporan/stokout">Stok Out</a>
                            </li>
                            <li class="<?=@$side35?>">
                                <a href="<?=base_url()?>admin/laporan/kaskeluar">Kas</a>
                            </li>
                            <li class="<?=@$side36?>">
                                <a href="<?=base_url()?>admin/laporan/pospengeluaran">Pos Pengeluaran</a>
                            </li>
                            <li class="<?=@$side37?>">
                                <a href="<?=base_url()?>admin/laporan/produkterlaris">Produk Terlaris</a>
                            </li>
                            <li class="<?=@$side38?>">
                                <a href="<?=base_url()?>admin/laporan/neraca">Neraca</a>
                            </li>
                            <li class="<?=@$side39?>">
                                <a href="<?=base_url()?>admin/laporan/labarugi">Laba-Rugi</a>
                            </li>
                        </ul>
                    </div>
                </li>
    			<li class="<?=@$mn_member?>">
    				<a href="<?=base_url()?>member">
    					<i class="material-icons">people</i>
    					<p>Member</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_tutup?>">
    				<a href="<?=base_url()?>staff/kas/tutupharian">
    					<i class="fas fa-file-invoice-dollar"></i>
    					<p>Rekapan Harian</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_pinjam?>">
    				<a href="<?=base_url()?>admin/pinjam">
    					<i class="fas fa-hand-holding-usd"></i>
    					<p>Stok Out</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_req?>">
    				<a href="<?=base_url()?>admin/moving">
    					<i class="fas fa-truck-loading"></i>
    					<p>Request Barang</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_confirm?>">
    				<a href="<?=base_url()?>admin/moving/konfirm">
    					<i class="fas fa-truck-moving"></i>
    					<p>Konfirmasi Permintaan</p>
    				</a>
    			</li>
            <?php }elseif (@$_SESSION["logged_status"]["role"]=="Office Manager"){?>
                <li class="<?=@$mn_dash?>">
                    <a href="<?=base_url()?>dashboard">
                        <i class="material-icons">dashboard</i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="<?=@$mn_setting?>">
                    <a data-toggle="collapse" href="#settings" aria-expanded="<?php echo (@$mn_setting=="active")?"true":"false" ?>">
                        <i class="material-icons">settings</i>
                        <p>Setup
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="<?=@$colset?>" id="settings">
                        <ul class="nav">
                            <li class="<?=@$side1?>">
                                <a href="<?=base_url()?>admin/pengguna">Pengguna</a>
                            </li>
                            <li class="<?=@$side2?>">
                                <a href="<?=base_url()?>admin/store">Store</a>
                            </li>
                            <li class="<?=@$side3?>">
                                <a href="<?=base_url()?>admin/brand">Brand</a>
                            </li>
                            <li class="<?=@$side4?>">
                                <a href="<?=base_url()?>admin/kategori">Kategori</a>
                            </li>
                            <li class="<?=@$side5?>">
                                <a href="<?=base_url()?>admin/size">Size</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="<?=@$mn_master?>">
                    <a data-toggle="collapse" href="#master" aria-expanded="<?php echo (@$mn_master=="active")?"true":"false" ?>">
                        <i class="material-icons">library_add</i>
                        <p>Master Data
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="<?=@$colmas?>" id="master">
                        <ul class="nav">
                            <li class="<?=@$side6?>">
                                <a href="<?=base_url()?>admin/assignstaff">Staff</a>
                            </li>
                            <li class="<?=@$side7?>">
                                <a href="<?=base_url()?>admin/produk">Produk</a>
                            </li>
                            <li class="<?=@$side8?>">
                                <a href="<?=base_url()?>admin/produksize">Produk-Size</a>
                            </li>
                            <li class="<?=@$side9?>">
                                <a href="<?=base_url()?>admin/stok">Stok Awal</a>
                            </li>
                            <li class="<?=@$side16?>">
                                <a href="<?=base_url()?>admin/stok/restok">Restock</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="<?=@$mn_laporan?>">
                    <a data-toggle="collapse" href="#laporan" aria-expanded="<?php echo (@$mn_laporan=="active")?"true":"false" ?>">
                        <i class="material-icons">settings</i>
                        <p>Laporan
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="<?=@$collap?>" id="laporan">
                        <ul class="nav">
                            <li class="<?=@$side10?>">
                                <a href="<?=base_url()?>admin/laporan/mutasi">Persediaan Global</a>
                            </li>
                            <li class="<?=@$side20?>">
                                <a href="<?=base_url()?>admin/laporan/mutasidetail">Persediaan Detail</a>
                            </li>
                            <li class="<?=@$side11?>">
                                <a href="<?=base_url()?>admin/laporan/penjualan">Penjualan Summary</a>
                            </li>
                            <li class="<?=@$side15?>">
                                <a href="<?=base_url()?>admin/laporan/brand">Penjualan Brand</a>
                            </li>
                            <li class="<?=@$side12?>">
                                <a href="<?=base_url()?>admin/laporan/barang">Drop In/Drop Out</a>
                            </li>
                            <li class="<?=@$side14?>">
                                <a href="<?=base_url()?>admin/laporan/nontunai">Non Tunai</a>
                            </li>
                            <li class="<?=@$side17?>">
                                <a href="<?=base_url()?>admin/laporan/request">Permintaan</a>
                            </li>
                            <li class="<?=@$side18?>">
                                <a href="<?=base_url()?>admin/laporan/retur">Retur Konsumen</a>
                            </li>
                            <li class="<?=@$side19?>">
                                <a href="<?=base_url()?>admin/laporan/stokout">Stok Out</a>
                            </li>
                            <li class="<?=@$side21?>">
                                <a href="<?=base_url()?>admin/laporan/kaskeluar">Kas</a>
                            </li>
                        </ul>
                    </div>
                </li>
    			<li class="<?=@$mn_member?>">
    				<a href="<?=base_url()?>member">
    					<i class="material-icons">people</i>
    					<p>Member</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_req?>">
    				<a href="<?=base_url()?>admin/moving">
    					<i class="fas fa-truck-loading"></i>
    					<p>Request Barang</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_confirm?>">
    				<a href="<?=base_url()?>admin/moving/konfirm">
    					<i class="fas fa-truck-moving"></i>
    					<p>Konfirm Permintaan</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_appopname?>">
    				<a href="<?=base_url()?>admin/opname/konfirm">
    					<i class="fas fa-truck-moving"></i>
    					<p>Konfirm Opname</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_tutup?>">
    				<a href="<?=base_url()?>staff/kas/tutupharian">
    					<i class="fas fa-file-invoice-dollar"></i>
    					<p>Rekapan Harian</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_pinjam?>">
    				<a href="<?=base_url()?>admin/pinjam">
    					<i class="fas fa-hand-holding-usd"></i>
    					<p>Stok Out</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_opname?>">
    				<a href="<?=base_url()?>admin/opname">
    					<i class="fas fa-box"></i>
    					<p>Penyesuaian Stok</p>
    				</a>
    			</li>
            <?php }elseif (@$_SESSION["logged_status"]["role"]=="Office Staff"){?>
                <li class="<?=@$mn_dash?>">
                    <a href="<?=base_url()?>dashboard">
                        <i class="material-icons">dashboard</i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="<?=@$mn_laporan?>">
                    <a data-toggle="collapse" href="#laporan" aria-expanded="<?php echo (@$mn_laporan=="active")?"true":"false" ?>">
                        <i class="material-icons">settings</i>
                        <p>Laporan
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="<?=@$collap?>" id="laporan">
                        <ul class="nav">
                            <li class="<?=@$side10?>">
                                <a href="<?=base_url()?>admin/laporan/mutasi">Persediaan Global</a>
                            </li>
                            <li class="<?=@$side20?>">
                                <a href="<?=base_url()?>admin/laporan/mutasidetail">Persediaan Detail</a>
                            </li>
                        </ul>
                    </div>
                </li>
    			<li class="<?=@$mn_tutup?>">
    				<a href="<?=base_url()?>staff/kas/tutupharian">
    					<i class="fas fa-file-invoice-dollar"></i>
    					<p>Rekapan Harian</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_req?>">
    				<a href="<?=base_url()?>admin/moving">
    					<i class="fas fa-truck-loading"></i>
    					<p>Request Barang</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_confirm?>">
    				<a href="<?=base_url()?>admin/moving/konfirm">
    					<i class="fas fa-truck-moving"></i>
    					<p>Konfirm Permintaan</p>
    				</a>
    			</li>
            <?php }elseif (@$_SESSION["logged_status"]["role"]=="Store Manager"){?>
                <li class="<?=@$mn_dash?>">
                    <a href="<?=base_url()?>dashboard">
                        <i class="material-icons">dashboard</i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="<?=@$mn_laporan?>">
                    <a data-toggle="collapse" href="#laporan" aria-expanded="<?php echo (@$mn_laporan=="active")?"true":"false" ?>">
                        <i class="material-icons">settings</i>
                        <p>Laporan
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="<?=@$collap?>" id="laporan">
                        <ul class="nav">
                            <li class="<?=@$side10?>">
                                <a href="<?=base_url()?>admin/laporan/mutasi">Persediaan Global</a>
                            </li>
                            <li class="<?=@$side20?>">
                                <a href="<?=base_url()?>admin/laporan/mutasidetail">Persediaan Detail</a>
                            </li>
                            <li class="<?=@$side11?>">
                                <a href="<?=base_url()?>admin/laporan/penjualan">Penjualan Summary</a>
                            </li>
                            <li class="<?=@$side15?>">
                                <a href="<?=base_url()?>admin/laporan/brand">Penjualan Brand</a>
                            </li>
                            <li class="<?=@$side12?>">
                                <a href="<?=base_url()?>admin/laporan/barang">Drop In/Drop Out</a>
                            </li>
                            <li class="<?=@$side14?>">
                                <a href="<?=base_url()?>admin/laporan/nontunai">Non Tunai</a>
                            </li>
                            <li class="<?=@$side17?>">
                                <a href="<?=base_url()?>admin/laporan/request">Permintaan</a>
                            </li>
                            <li class="<?=@$side18?>">
                                <a href="<?=base_url()?>admin/laporan/retur">Retur Konsumen</a>
                            </li>
                            <li class="<?=@$side19?>">
                                <a href="<?=base_url()?>admin/laporan/stokout">Stok Out</a>
                            </li>
                            <li class="<?=@$side21?>">
                                <a href="<?=base_url()?>admin/laporan/kaskeluar">Kas</a>
                            </li>
                        </ul>
                    </div>
                </li>
    			<li class="<?=@$mn_member?>">
    				<a href="<?=base_url()?>member">
    					<i class="material-icons">people</i>
    					<p>Member</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_opname?>">
    				<a href="<?=base_url()?>admin/opname">
    					<i class="fas fa-box"></i>
    					<p>Stok Opname</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_bayar?>">
    				<a href="<?=base_url()?>admin/bayar">
    					<i class="fas fa-undo"></i>
    					<p>Ganti Cara Bayar</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_pinjam?>">
    				<a href="<?=base_url()?>admin/pinjam">
    					<i class="fas fa-hand-holding-usd"></i>
    					<p>Stok Out</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_req?>">
    				<a href="<?=base_url()?>admin/moving">
    					<i class="fas fa-truck-loading"></i>
    					<p>Request Barang</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_confirm?>">
    				<a href="<?=base_url()?>admin/moving/konfirm">
    					<i class="fas fa-truck-moving"></i>
    					<p>Konfirm Permintaan</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_cashier?>">
    				<a href="<?=base_url()?>staff/cashier">
    					<i class="fas fa-money-bill-wave"></i>
    					<p>Penjualan</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_cash?>">
    				<a href="<?=base_url()?>staff/kas">
    					<i class="fas fa-wallet"></i>
    					<p>Kas</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_tutup?>">
    				<a href="<?=base_url()?>staff/kas/tutupharian">
    					<i class="fas fa-file-invoice-dollar"></i>
    					<p>Rekapan Harian</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_retur?>">
    				<a href="<?=base_url()?>staff/retur">
    					<i class="fas fa-exchange-alt"></i>
    					<p>Retur Customer</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_cari?>">
    				<a href="<?=base_url()?>staff/pencarian">
    					<i class="material-icons">zoom_in</i>
    					<p>Pencarian</p>
    				</a>
    			</li>
            <?php }else{?>
                <li class="<?=@$mn_dash?>">
                    <a href="<?=base_url()?>staff/dashboard">
                        <i class="material-icons">dashboard</i>
                        <p>Dashboard</p>
                    </a>
                </li>
    			<li class="<?=@$mn_cashier?>">
    				<a href="<?=base_url()?>staff/cashier">
    					<i class="fas fa-money-bill-wave"></i>
    					<p>Penjualan</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_member?>">
    				<a href="<?=base_url()?>member">
    					<i class="material-icons">people</i>
    					<p>Member</p>
    				</a>
    			</li>
                <li class="<?=@$mn_laporan?>">
                    <a data-toggle="collapse" href="#laporan" aria-expanded="<?php echo (@$mn_laporan=="active")?"true":"false" ?>">
                        <i class="material-icons">settings</i>
                        <p>Laporan
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="<?=@$collap?>" id="laporan">
                        <ul class="nav">
                            <li class="<?=@$side10?>">
                                <a href="<?=base_url()?>admin/laporan/mutasi">Persediaan Global</a>
                            </li>
                            <li class="<?=@$side20?>">
                                <a href="<?=base_url()?>admin/laporan/mutasidetail">Persediaan Detail</a>
                            </li>
                            <li class="<?=@$side11?>">
                                <a href="<?=base_url()?>admin/laporan/penjualan">Penjualan Summary</a>
                            </li>
                            <li class="<?=@$side15?>">
                                <a href="<?=base_url()?>admin/laporan/brand">Penjualan Brand</a>
                            </li>
                            <li class="<?=@$side12?>">
                                <a href="<?=base_url()?>admin/laporan/barang">Drop In/Drop Out</a>
                            </li>
                            <li class="<?=@$side14?>">
                                <a href="<?=base_url()?>admin/laporan/nontunai">Non Tunai</a>
                            </li>
                            <li class="<?=@$side17?>">
                                <a href="<?=base_url()?>admin/laporan/request">Permintaan</a>
                            </li>
                            <li class="<?=@$side18?>">
                                <a href="<?=base_url()?>admin/laporan/retur">Retur Konsumen</a>
                            </li>
                            <li class="<?=@$side19?>">
                                <a href="<?=base_url()?>admin/laporan/stokout">Stok Out</a>
                            </li>
                        </ul>
                    </div>
                </li>
    			<li class="<?=@$mn_cash?>">
    				<a href="<?=base_url()?>staff/kas">
    					<i class="fas fa-wallet"></i>
    					<p>Kas</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_tutup?>">
    				<a href="<?=base_url()?>staff/kas/tutupharian">
    					<i class="fas fa-file-invoice-dollar"></i>
    					<p>Rekapan Harian</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_req?>">
    				<a href="<?=base_url()?>admin/moving">
    					<i class="fas fa-box"></i>
    					<p>Request Barang</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_retur?>">
    				<a href="<?=base_url()?>staff/retur">
                        <i class="fas fa-exchange-alt"></i>
    					<p>Retur Customer</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_pinjam?>">
    				<a href="<?=base_url()?>admin/pinjam">
    					<i class="fas fa-hand-holding-usd"></i>
    					<p>Stok Out</p>
    				</a>
    			</li>
    			<li class="<?=@$mn_cari?>">
    				<a href="<?=base_url()?>staff/pencarian">
    					<i class="material-icons">zoom_in</i>
    					<p>Pencarian</p>
    				</a>
    			</li>
            <?php } ?>
            <li>
                <a href="<?= site_url('auth/auth_logout') ?>">
                    <i class="material-icons">exit_to_app</i>
                    <p>Logout</p>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="main-panel">
	<nav class="navbar navbar-transparent navbar-absolute">
		<div class="container-fluid">
			<div class="navbar-minimize">
				<button id="minimizeSidebar" class="btn btn-round btn-white btn-fill btn-just-icon">
					<i class="material-icons visible-on-sidebar-regular">more_vert</i>
					<i class="material-icons visible-on-sidebar-mini">view_list</i>
				</button>
			</div>
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#"> <?=$title?> </a>
			</div>
		</div>
	</nav>

	<div class="content">
		<div class="container-fluid">

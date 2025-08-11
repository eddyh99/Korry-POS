

<?php
$session = session();

// Header
echo view('layout/header');

// Sidebar hanya untuk user yang login & bukan halaman cashier
if ($session->get('logged_status') && service('uri')->getSegment(2) !== 'cashier') {
    echo view('layout/sidebar');
}

// Konten utama
if (isset($content)) {
    echo view($content);
}

// Footer
echo view('layout/footer');

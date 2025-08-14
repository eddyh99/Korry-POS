<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class MovingModel extends Model
{
    protected $produk = 'produk';
    protected $pindah = 'pindah';
    protected $pindah_detail = 'pindah_detail';
    protected $store = 'store';

    protected $opnameModel;

    public function setOpnameModel($model)
    {
        // inject OpnameModel dari controller Moving.php
        $this->opnameModel = $model;
    }

    public function allposts_count()
    {
        $storeid = @$_SESSION["logged_status"]["storeid"];
        $role    = @$_SESSION["logged_status"]["role"];

        if ($role == "Store Manager" || $role == "Staff") {
            $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan, 
                        IF(x.approved=1, 'Diterima', IF(x.approved=2, 'Batal', IF(x.approved=3, 'Dikirim', 'Belum'))) as status
                    FROM (
                        SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                        FROM {$this->pindah} a
                        INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                        WHERE a.tujuan=?
                    ) x
                    INNER JOIN {$this->store} y ON x.dari=y.storeid";
            $query = $this->db->query($sql, [$storeid]);
        } else {
            $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan, 
                        IF(x.approved=1, 'Diterima', IF(x.approved=2, 'Batal', IF(x.approved=3, 'Dikirim', 'Belum'))) as status
                    FROM (
                        SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                        FROM {$this->pindah} a
                        INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                    ) x
                    INNER JOIN {$this->store} y ON x.dari=y.storeid";
            $query = $this->db->query($sql);
        }

        return $query->getNumRows(); // CI4 style
    }

    public function allposts($limit, $start, $col, $dir)
    {
        $storeid = @$_SESSION["logged_status"]["storeid"];
        $role    = @$_SESSION["logged_status"]["role"];
        $month   = date("m");
        $year    = date("Y");

        if ($role == "Store Manager" || $role == "Staff") {
            $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan, 
                        IF(x.approved=1, 'Diterima', IF(x.approved=2, 'Batal', IF(x.approved=3, 'Dikirim', 'Belum'))) as status
                    FROM (
                        SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                        FROM {$this->pindah} a
                        INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                        WHERE a.tujuan=? 
                    ) x
                    INNER JOIN {$this->store} y ON x.dari=y.storeid
                    WHERE MONTH(x.tanggal)=? AND YEAR(x.tanggal)=?
                    ORDER BY {$col} {$dir} LIMIT {$start}, {$limit}";
            $query = $this->db->query($sql, [$storeid, $month, $year]);
        } else {
            $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan, 
                        IF(x.approved=1, 'Diterima', IF(x.approved=2, 'Batal', IF(x.approved=3, 'Dikirim', 'Belum'))) as status
                    FROM (
                        SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                        FROM {$this->pindah} a
                        INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                    ) x
                    INNER JOIN {$this->store} y ON x.dari=y.storeid
                    WHERE MONTH(x.tanggal)=? AND YEAR(x.tanggal)=?
                    ORDER BY {$col} {$dir} LIMIT {$start}, {$limit}";
            $query = $this->db->query($sql, [$month, $year]);
        }

        return $query->getResultArray(); // CI4 style
    }

    public function posts_search($limit, $start, $search, $col, $dir)
    {
        $storeid = @$_SESSION["logged_status"]["storeid"];
        $role    = @$_SESSION["logged_status"]["role"];
        $month   = date("m");
        $year    = date("Y");
        $like    = "%{$search}%";

        if ($role == "Store Manager" || $role == "Staff") {
            $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan, 
                        IF(x.approved=1, 'Diterima', IF(x.approved=2, 'Batal', IF(x.approved=3, 'Dikirim', 'Belum'))) as status
                    FROM (
                        SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                        FROM {$this->pindah} a
                        INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                        WHERE a.tujuan=?
                    ) x
                    INNER JOIN {$this->store} y ON x.dari=y.storeid
                    WHERE MONTH(x.tanggal)=? AND YEAR(x.tanggal)=?
                    AND (mutasi_id LIKE ? OR tanggal LIKE ? OR dari LIKE ? OR tujuan LIKE ?)
                    ORDER BY {$col} {$dir} LIMIT {$start}, {$limit}";
            $query = $this->db->query($sql, [$storeid, $month, $year, $like, $like, $like, $like]);
        } else {
            $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan, 
                        IF(x.approved=1, 'Diterima', IF(x.approved=2, 'Batal', IF(x.approved=3, 'Dikirim', 'Belum'))) as status
                    FROM (
                        SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                        FROM {$this->pindah} a
                        INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                    ) x
                    INNER JOIN {$this->store} y ON x.dari=y.storeid
                    WHERE MONTH(x.tanggal)=? AND YEAR(x.tanggal)=?
                    AND (mutasi_id LIKE ? OR tanggal LIKE ? OR dari LIKE ? OR tujuan LIKE ?)
                    ORDER BY {$col} {$dir} LIMIT {$start}, {$limit}";
            $query = $this->db->query($sql, [$month, $year, $like, $like, $like, $like]);
        }

        return $query->getResultArray(); // CI4 style
    }
    // 
    public function posts_search_count($search)
{
    $storeid = @$_SESSION["logged_status"]["storeid"];
    $role    = @$_SESSION["logged_status"]["role"];
    $month   = date("m");
    $year    = date("Y");
    $like    = "%{$search}%";

    if ($role == "Store Manager" || $role == "Staff") {
        $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan,
                    IF(x.approved=1,'Diterima',IF(x.approved=2,'Batal',IF(x.approved=3,'Dikirim','Belum'))) as status
                FROM (
                    SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                    FROM {$this->pindah} a
                    INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                    WHERE a.tujuan=?
                ) x
                INNER JOIN {$this->store} y ON x.dari=y.storeid
                WHERE MONTH(x.tanggal)=? AND YEAR(x.tanggal)=?
                  AND (mutasi_id LIKE ? OR tanggal LIKE ? OR dari LIKE ? OR tujuan LIKE ?)";
        $params = [$storeid, $month, $year, $like, $like, $like, $like];
    } else {
        $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan,
                    IF(x.approved=1,'Diterima',IF(x.approved=2,'Batal',IF(x.approved=3,'Dikirim','Belum'))) as status
                FROM (
                    SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                    FROM {$this->pindah} a
                    INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                ) x
                INNER JOIN {$this->store} y ON x.dari=y.storeid
                WHERE MONTH(x.tanggal)=? AND YEAR(x.tanggal)=?
                  AND (mutasi_id LIKE ? OR tanggal LIKE ? OR dari LIKE ? OR tujuan LIKE ?)";
        $params = [$month, $year, $like, $like, $like, $like];
    }

    $query = $this->db->query($sql, $params);
    return $query->getNumRows();
}

public function allposts_countkonfirm()
{
    $storeid = @$_SESSION["logged_status"]["storeid"];
    $role    = @$_SESSION["logged_status"]["role"];
    $month   = date("m");
    $year    = date("Y");

    if ($role == "Store Manager" || $role == "Staff") {
        $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan,
                    IF(x.approved=1,'Diterima',IF(x.approved=2,'Batal',IF(x.approved=3,'Dikirim','Belum'))) as status
                FROM (
                    SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                    FROM {$this->pindah} a
                    INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                ) x
                INNER JOIN {$this->store} y ON x.dari=y.storeid
                WHERE MONTH(x.tanggal)=? AND YEAR(x.tanggal)=? AND x.dari=?";
        $params = [$month, $year, $storeid];
    } else {
        $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan,
                    IF(x.approved=1,'Diterima',IF(x.approved=2,'Batal',IF(x.approved=3,'Dikirim','Belum'))) as status
                FROM (
                    SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                    FROM {$this->pindah} a
                    INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                ) x
                INNER JOIN {$this->store} y ON x.dari=y.storeid
                WHERE MONTH(x.tanggal)=? AND YEAR(x.tanggal)=?";
        $params = [$month, $year];
    }

    $query = $this->db->query($sql, $params);
    return $query->getNumRows();
}
// 
public function allpostskonfirm($limit, $start, $col, $dir)
{
    $storeid = @$_SESSION["logged_status"]["storeid"];
    $role    = @$_SESSION["logged_status"]["role"];
    $month   = date("m");
    $year    = date("Y");

    if ($role == "Store Manager" || $role == "Staff") {
        $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan,
                    IF(x.approved=1,'Diterima',IF(x.approved=2,'Batal',IF(x.approved=3,'Dikirim','Belum'))) as status
                FROM (
                    SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                    FROM {$this->pindah} a
                    INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                ) x
                INNER JOIN {$this->store} y ON x.dari=y.storeid
                WHERE MONTH(x.tanggal)=? AND YEAR(x.tanggal)=? AND x.dari=?
                ORDER BY {$col} {$dir} LIMIT {$start}, {$limit}";
        $params = [$month, $year, $storeid];
    } else {
        $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan,
                    IF(x.approved=1,'Diterima',IF(x.approved=2,'Batal',IF(x.approved=3,'Dikirim','Belum'))) as status
                FROM (
                    SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                    FROM {$this->pindah} a
                    INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                ) x
                INNER JOIN {$this->store} y ON x.dari=y.storeid
                WHERE MONTH(x.tanggal)=? AND YEAR(x.tanggal)=?
                ORDER BY {$col} {$dir} LIMIT {$start}, {$limit}";
        $params = [$month, $year];
    }

    $query = $this->db->query($sql, $params);
    return $query->getResultArray();
}

public function posts_searchkonfirm($limit, $start, $search, $col, $dir)
{
    $storeid = @$_SESSION["logged_status"]["storeid"];
    $role    = @$_SESSION["logged_status"]["role"];
    $month   = date("m");
    $year    = date("Y");
    $like    = "%{$search}%";

    if ($role == "Store Manager" || $role == "Staff") {
        $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan,
                    IF(x.approved=1,'Diterima',IF(x.approved=2,'Batal',IF(x.approved=3,'Dikirim','Belum'))) as status
                FROM (
                    SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                    FROM {$this->pindah} a
                    INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                ) x
                INNER JOIN {$this->store} y ON x.dari=y.storeid
                WHERE x.dari=? AND (mutasi_id LIKE ? OR tanggal LIKE ? OR dari LIKE ? OR tujuan LIKE ?)
                ORDER BY {$col} {$dir} LIMIT {$start}, {$limit}";
        $params = [$storeid, $like, $like, $like, $like];
    } else {
        $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan,
                    IF(x.approved=1,'Diterima',IF(x.approved=2,'Batal',IF(x.approved=3,'Dikirim','Belum'))) as status
                FROM (
                    SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                    FROM {$this->pindah} a
                    INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                ) x
                INNER JOIN {$this->store} y ON x.dari=y.storeid
                WHERE MONTH(x.tanggal)=? AND YEAR(x.tanggal)=? AND (mutasi_id LIKE ? OR tanggal LIKE ? OR dari LIKE ? OR tujuan LIKE ?)
                ORDER BY {$col} {$dir} LIMIT {$start}, {$limit}";
        $params = [$month, $year, $like, $like, $like, $like];
    }

    $query = $this->db->query($sql, $params);
    return $query->getResultArray();
}

public function posts_search_countkonfirm($search)
{
    $storeid = @$_SESSION["logged_status"]["storeid"];
    $role    = @$_SESSION["logged_status"]["role"];
    $month   = date("m");
    $year    = date("Y");
    $like    = "%{$search}%";

    if ($role == "Store Manager" || $role == "Staff") {
        $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan,
                    IF(x.approved=1,'Diterima',IF(x.approved=2,'Batal',IF(x.approved=3,'Dikirim','Belum'))) as status
                FROM (
                    SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                    FROM {$this->pindah} a
                    INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                ) x
                INNER JOIN {$this->store} y ON x.dari=y.storeid
                WHERE x.dari=? AND (mutasi_id LIKE ? OR tanggal LIKE ? OR dari LIKE ? OR tujuan LIKE ?)";
        $params = [$storeid, $like, $like, $like, $like];
    } else {
        $sql = "SELECT x.mutasi_id, x.tanggal, y.store as dari, x.tujuan,
                    IF(x.approved=1,'Diterima',IF(x.approved=2,'Batal',IF(x.approved=3,'Dikirim','Belum'))) as status
                FROM (
                    SELECT a.mutasi_id, a.tanggal, a.approved, a.dari, b.store as tujuan
                    FROM {$this->pindah} a
                    INNER JOIN {$this->store} b ON a.tujuan=b.storeid
                ) x
                INNER JOIN {$this->store} y ON x.dari=y.storeid
                WHERE MONTH(x.tanggal)=? AND YEAR(x.tanggal)=? AND (mutasi_id LIKE ? OR tanggal LIKE ? OR dari LIKE ? OR tujuan LIKE ?)";
        $params = [$month, $year, $like, $like, $like, $like];
    }

    $query = $this->db->query($sql, $params);
    return $query->getNumRows();
}
// 
public function insertData($pindah, $barang)
{
    $this->db->transStart(); 
    
    // insert ke tabel pindah
    $builder = $this->db->table($this->pindah);
    $builder->insert($pindah);
    $id = $this->db->insertID();

    foreach ($barang as $dt) {
        $temp = [
            "mutasi_id" => $id,
            "barcode"   => $dt[0],
            "size"      => strtoupper($dt[2])
        ];

        $stok = $this->opnameModel->getStok($dt[0], $pindah["dari"], strtoupper($dt[2]));
        $temp["jumlah"] = ($stok - $dt[3] < 0) ? $stok : $dt[3];

        $this->db->table($this->pindah_detail)->insert($temp);
        // Jika mau debug error bisa gunakan:
        // print_r($this->db->error());
    }

    $this->db->transComplete();

    if ($this->db->transStatus() === FALSE) {
        $this->db->transRollback();
        return FALSE;
    } else {
        $this->db->transCommit();
        return ["code" => 0, "message" => ""];
    }
}

public function voidData($data, $mutasi_id)
{
    $builder = $this->db->table($this->pindah);
    $builder->where("mutasi_id", $mutasi_id);
    if ($builder->update($data)) {
        return ["code" => 0, "message" => ""];
    } else {
        return $this->db->error();
    }
}

public function acceptData($data, $mutasi_id)
{
    $builder = $this->db->table($this->pindah);
    $builder->where("mutasi_id", $mutasi_id);
    if ($builder->update($data)) {
        return ["code" => 0, "message" => ""];
    } else {
        return $this->db->error();
    }
}

public function getMoving($mutasi_id)
{
    $sql = "SELECT x.store as asal, y.store as tujuan
            FROM 
            (SELECT store FROM {$this->pindah} a INNER JOIN {$this->store} b ON a.dari=b.storeid WHERE a.mutasi_id=?) x,
            (SELECT store FROM {$this->pindah} a INNER JOIN {$this->store} b ON a.tujuan=b.storeid WHERE a.mutasi_id=?) y";
    $query = $this->db->query($sql, [$mutasi_id, $mutasi_id]);
    return $query->getResultArray();
}

public function getdetail($mutasi_id)
{
    $sql = "SELECT a.*, b.namaproduk, b.namabrand
            FROM {$this->pindah_detail} a
            INNER JOIN {$this->produk} b ON a.barcode=b.barcode
            WHERE a.mutasi_id=?";
    $query = $this->db->query($sql, [$mutasi_id]);
    return $query->getResultArray();
}


}

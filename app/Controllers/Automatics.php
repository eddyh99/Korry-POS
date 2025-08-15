<?php

namespace App\Controllers;

use App\Models\Staff\KasModel;

use App\Controllers\BaseApiController;

class Automatics extends BaseApiController
{
    protected $kasModel;

    public function __construct()
    {
        $this->kasModel = new KasModel();
    }

    public function autoclose()
    {
        $now = date("Y-m-d");
        $notclose = $this->kasModel->notclosedstore();

        foreach ($notclose as $storeData) {
            $result = $this->kasModel->lastSaldo($now, $storeData["storeid"]);

            $kaskeluar = 0;
            foreach ($result["kas"] as $kasItem) {
                if ($kasItem["jenis"] === "Keluar") {
                    $kaskeluar += $kasItem["nominal"];
                }
            }

            $kasmasuk = 0;
            foreach ($result["kas"] as $kasItem) {
                if ($kasItem["jenis"] === "Masuk") {
                    $kasmasuk += $kasItem["nominal"];
                }
            }

            $totaltunai = $result["tunai"] + $result["saldo"] - $kaskeluar + $kasmasuk - $result["retur"]["tunai"];
            $setor = floor($totaltunai / 50000) * 50000;

            $data = [
                "nominal"    => $totaltunai - $setor,
                "jenis"      => "Kas Sisa",
                "dateonly"   => $now,
                "tanggal"    => date("Y-m-d H:i:s"),
                "storeid"    => $storeData["storeid"],
                "keterangan" => "Sisa Kas",
                "userid"     => 'admin'
            ];

            $this->kasModel->setSisa($data);
        }
    }
}

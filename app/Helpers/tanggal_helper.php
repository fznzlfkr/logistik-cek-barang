<?php

function formatTanggalIndo($tanggal)
{
    if (!$tanggal) {
        return '-';
    }

    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $time = strtotime($tanggal);
    $tgl  = date('d', $time);
    $bln  = $bulan[(int)date('m', $time)];
    $thn  = date('Y', $time);
    $jam  = date('H:i', $time);

    return "$tgl $bln $thn, $jam";
}

<?php
    // Connection, Session dan Helper
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";
    include "../../_Config/Setting.php";

    // Tangkap Periode Awal dan Akhir
    if(empty($_GET['periode_awal'])){
        echo 'Periode Awal Tidak Boleh Kosong!';
        exit;
    }
    if(empty($_GET['periode_akhir'])){
        echo 'Periode Akhir Tidak Boleh Kosong!';
        exit;
    }

    // Buat Variabel
    $periode_awal = trim($_GET['periode_awal'] ?? '');
    $periode_akhir = trim($_GET['periode_akhir'] ?? '');

    // Validasi period
    $periode_awal_obj  = DateTime::createFromFormat('Y-m-d', $periode_awal);
    $periode_akhir_obj = DateTime::createFromFormat('Y-m-d', $periode_akhir);

    if (!$periode_awal_obj || $periode_awal_obj->format('Y-m-d') !== $periode_awal || !$periode_akhir_obj || $periode_akhir_obj->format('Y-m-d') !== $periode_akhir) {
        echo 'Format periode tidak valid.';
        exit;
    }

    if ($periode_akhir_obj < $periode_awal_obj) {
        echo 'Periode Data Tidak Valid';
        exit;
    }

    // Format periode
    $periode_awal_format  = date('d F Y', strtotime($periode_awal));
    $periode_akhir_format = date('d F Y', strtotime($periode_akhir));

    $periode_awal_query  = $periode_awal . ' 00:00:00';
    $periode_akhir_query = $periode_akhir . ' 23:59:59';

    $genderCounts = [
        'Male' => 0,
        'Female' => 0
    ];
    $tujuanCounts = [
        'Rajal' => 0,
        'Ranap' => 0
    ];
    $ageCounts = [
        'remaja' => 0,
        'dewasa_awal' => 0,
        'dewasa_akhir' => 0,
        'lansia' => 0
    ];
    $totalRespondents = 0;

    $sql = "SELECT respondent_sex, kunjungan_tujuan, respondent_brithdate, tanggal_kunjungan FROM respondent WHERE tanggal_kunjungan BETWEEN ? AND ?";
    $stmt = $Conn->prepare($sql);
    if (!$stmt) {
        echo 'Gagal mempersiapkan query data.';
        exit;
    }

    $stmt->bind_param('ss', $periode_awal_query, $periode_akhir_query);
    if (!$stmt->execute()) {
        echo 'Gagal mengambil data responden.';
        $stmt->close();
        exit;
    }

    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $totalRespondents++;

        $sex = (string)($row['respondent_sex'] ?? '');
        if ($sex === 'Male') {
            $genderCounts['Male']++;
        } elseif ($sex === 'Female') {
            $genderCounts['Female']++;
        }

        $tujuan = (string)($row['kunjungan_tujuan'] ?? '');
        if ($tujuan === 'Rajal') {
            $tujuanCounts['Rajal']++;
        } elseif ($tujuan === 'Ranap') {
            $tujuanCounts['Ranap']++;
        }

        $respondent_brithdate = $row['respondent_brithdate'];
        $tanggal_kunjungan = $row['tanggal_kunjungan'];

        if (!empty($respondent_brithdate) && strtotime($respondent_brithdate) !== false && !empty($tanggal_kunjungan) && strtotime($tanggal_kunjungan) !== false) {
            $tgl_lahir = new DateTime($respondent_brithdate);
            $tgl_kunjungan = new DateTime($tanggal_kunjungan);
            if ($tgl_kunjungan >= $tgl_lahir) {
                $usia = $tgl_lahir->diff($tgl_kunjungan)->y;
                if ($usia < 20) {
                    $ageCounts['remaja']++;
                } elseif ($usia < 40) {
                    $ageCounts['dewasa_awal']++;
                } elseif ($usia < 60) {
                    $ageCounts['dewasa_akhir']++;
                } else {
                    $ageCounts['lansia']++;
                }
            }
        }
    }
    $stmt->close();

    $genderMale = $genderCounts['Male'];
    $genderFemale = $genderCounts['Female'];
    $jumlahRajal = $tujuanCounts['Rajal'];
    $jumlahRanap = $tujuanCounts['Ranap'];
    $jumlahRemaja = $ageCounts['remaja'];
    $jumlahDewasaAwal = $ageCounts['dewasa_awal'];
    $jumlahDewasaAkhir = $ageCounts['dewasa_akhir'];
    $jumlahLansia = $ageCounts['lansia'];

    $genderMalePercent = $totalRespondents ? number_format(($genderMale / $totalRespondents) * 100, 2) : '0.00';
    $genderFemalePercent = $totalRespondents ? number_format(($genderFemale / $totalRespondents) * 100, 2) : '0.00';
    $tujuanRajalPercent = $totalRespondents ? number_format(($jumlahRajal / $totalRespondents) * 100, 2) : '0.00';
    $tujuanRanapPercent = $totalRespondents ? number_format(($jumlahRanap / $totalRespondents) * 100, 2) : '0.00';
    $remajaPercent = $totalRespondents ? number_format(($jumlahRemaja / $totalRespondents) * 100, 2) : '0.00';
    $dewasaAwalPercent = $totalRespondents ? number_format(($jumlahDewasaAwal / $totalRespondents) * 100, 2) : '0.00';
    $dewasaAkhirPercent = $totalRespondents ? number_format(($jumlahDewasaAkhir / $totalRespondents) * 100, 2) : '0.00';
    $lansiaPercent = $totalRespondents ? number_format(($jumlahLansia / $totalRespondents) * 100, 2) : '0.00';
    $totalPercent = $totalRespondents ? '100.00' : '0.00';

    $totalGender = $genderMale + $genderFemale;
    $totalTujuan = $jumlahRajal + $jumlahRanap;
    $totalUsia = $jumlahRemaja + $jumlahDewasaAwal + $jumlahDewasaAkhir + $jumlahLansia;

    // Mengatur Logo Dari Setting.php
    $path_logo = "$base_url/assets/img/logo/$company_logo";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laporan Deskripsi Responden</title>
        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 14px; /* Ukuran font dasar dinaikkan agar lebih terbaca */
                color: #000;
                margin: 30px;
                line-height: 1.4;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            /* Desain Terpola untuk Kop Surat Pas di Tengah */
            .kop-surat {
                width: 100%;
                margin-bottom: 5px;
            }
            .kop-surat td {
                vertical-align: middle;
            }
            .kop-logo {
                width: 15%;
                text-align: left;
            }
            .kop-text {
                width: 70%;
                text-align: center;
            }
            .kop-text h1 {
                margin: 0 0 5px 0;
                font-size: 22px;
                font-weight: bold;
                text-transform: uppercase;
            }
            .kop-text p {
                margin: 2px 0;
                font-size: 13px;
                color: #333;
            }
            .kop-spacer {
                width: 15%; /* Penyeimbang sisi kanan agar teks tengah murni */
            }

            .garis-kop {
                border: 0;
                border-top: 3px solid #000;
                border-bottom: 1px solid #000;
                height: 3px;
                margin: 10px 0 25px 0;
            }

            /* Judul Dokumen */
            .judul-laporan {
                text-align: center;
                margin-bottom: 30px;
            }
            .judul-laporan h2 {
                margin: 0 0 8px 0;
                font-size: 18px;
                font-weight: bold;
                letter-spacing: 0.5px;
            }
            .judul-laporan p {
                margin: 0;
                font-size: 14px;
            }

            /* Tabel Data Laporan */
            .report-table {
                width: 100%;
                margin-bottom: 25px;
            }
            .report-table th,
            .report-table td {
                border: 1px solid #000;
                padding: 8px 10px;
                font-size: 13px;
            }
            .report-table thead tr.section-header td {
                background-color: #f5f5f5;
                font-weight: bold;
                font-size: 14px;
                padding: 10px;
            }
            .report-table th {
                background-color: #fafafa;
                text-align: center;
                font-weight: bold;
            }
            .text-center {
                text-align: center;
            }
            .text-left {
                text-align: left;
            }
            .text-right {
                text-align: right;
            }

            /* Footer Cetak */
            .footer-cetak {
                margin-top: 40px;
                text-align: right;
                font-size: 12px;
                font-style: italic;
            }
        </style>
    </head>
    <body>
        <div class="report-container">
            
            <table class="kop-surat">
                <tr>
                    <td class="kop-logo">
                        <img src="<?php echo $path_logo;?>" width="100" alt="Logo">
                    </td>
                    <td class="kop-text">
                        <h1><?php echo $company_name;?></h1>
                        <p><?php echo $company_address;?></p>
                        <p>Telp: <?php echo $company_phone;?> | Email: <?php echo $company_email;?></p>
                    </td>
                    <td class="kop-spacer"></td>
                </tr>
            </table>

            <div class="garis-kop"></div>

            <div class="judul-laporan">
                <h2>LAPORAN DESKRIPSI RESPONDEN</h2>
                <p>Periode: <b><?php echo $periode_awal_format;?></b> s/d <b><?php echo $periode_akhir_format;?></b></p>
            </div>

            <div class="summary-card">
                
                <table class="report-table">
                    <thead>
                        <tr class="section-header">
                            <td colspan="4">A. Distribusi Responden Berdasarkan Gender</td>
                        </tr>
                        <tr>
                            <th width="8%">No</th>
                            <th>Gender</th>
                            <th width="25%">Jumlah</th>
                            <th width="25%">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">1</td>
                            <td class="text-left">Laki-laki</td>
                            <td class="text-center"><?php echo $genderMale; ?></td>
                            <td class="text-center"><?php echo $genderMalePercent; ?> %</td>
                        </tr>
                        <tr>
                            <td class="text-center">2</td>
                            <td class="text-left">Perempuan</td>
                            <td class="text-center"><?php echo $genderFemale; ?></td>
                            <td class="text-center"><?php echo $genderFemalePercent; ?> %</td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td class="text-center" colspan="2">TOTAL</td>
                            <td class="text-center"><?php echo $totalGender; ?></td>
                            <td class="text-center"><?php echo $totalPercent; ?> %</td>
                        </tr>
                    </tbody>
                </table>

                <table class="report-table">
                    <thead>
                        <tr class="section-header">
                            <td colspan="4">B. Distribusi Responden Berdasarkan Tujuan Kunjungan</td>
                        </tr>
                        <tr>
                            <th width="8%">No</th>
                            <th>Kunjungan</th>
                            <th width="25%">Jumlah</th>
                            <th width="25%">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">1</td>
                            <td class="text-left">Rawat Jalan</td>
                            <td class="text-center"><?php echo $jumlahRajal; ?></td>
                            <td class="text-center"><?php echo $tujuanRajalPercent; ?> %</td>
                        </tr>
                        <tr>
                            <td class="text-center">2</td>
                            <td class="text-left">Rawat Inap</td>
                            <td class="text-center"><?php echo $jumlahRanap; ?></td>
                            <td class="text-center"><?php echo $tujuanRanapPercent; ?> %</td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td class="text-center" colspan="2">TOTAL</td>
                            <td class="text-center"><?php echo $totalTujuan; ?></td>
                            <td class="text-center"><?php echo $totalPercent; ?> %</td>
                        </tr>
                    </tbody>
                </table>

                <table class="report-table">
                    <thead>
                        <tr class="section-header">
                            <td colspan="4">C. Distribusi Responden Berdasarkan Usia</td>
                        </tr>
                        <tr>
                            <th width="8%">No</th>
                            <th>Klasifikasi Usia</th>
                            <th width="25%">Jumlah</th>
                            <th width="25%">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">1</td>
                            <td class="text-left">Remaja (&lt; 20 Tahun)</td>
                            <td class="text-center"><?php echo $jumlahRemaja; ?></td>
                            <td class="text-center"><?php echo $remajaPercent; ?> %</td>
                        </tr>
                        <tr>
                            <td class="text-center">2</td>
                            <td class="text-left">Dewasa Awal (20 - 40 Tahun)</td>
                            <td class="text-center"><?php echo $jumlahDewasaAwal; ?></td>
                            <td class="text-center"><?php echo $dewasaAwalPercent; ?> %</td>
                        </tr>
                        <tr>
                            <td class="text-center">3</td>
                            <td class="text-left">Dewasa Akhir (40 - 60 Tahun)</td>
                            <td class="text-center"><?php echo $jumlahDewasaAkhir; ?></td>
                            <td class="text-center"><?php echo $dewasaAkhirPercent; ?> %</td>
                        </tr>
                        <tr>
                            <td class="text-center">4</td>
                            <td class="text-left">Lansia (&gt;= 60 Tahun)</td>
                            <td class="text-center"><?php echo $jumlahLansia; ?></td>
                            <td class="text-center"><?php echo $lansiaPercent; ?> %</td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td class="text-center" colspan="2">TOTAL</td>
                            <td class="text-center"><?php echo $totalUsia; ?></td>
                            <td class="text-center"><?php echo $totalPercent; ?> %</td>
                        </tr>
                    </tbody>
                </table>

            </div>

            <div class="footer-cetak">
                Dicetak tanggal: <?php echo date('d-m-Y H:i:s');?>
            </div>

        </div>
    </body>
</html>
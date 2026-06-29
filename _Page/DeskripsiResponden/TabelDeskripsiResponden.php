<?php
    // Connection, Session dan Helper
    include "../../_Config/Connection.php";
    include "../../_Config/Helper.php";
    include "../../_Config/Session.php";

    // Tangkap Periode Awal dan Akhir
    if(empty($_POST['periode_awal'])){
        echo '
            <div class="row align-items-center g-4">
                <div class="col-12 text-center text-danger">
                    Periode Awal Tidak Boleh Kosong!
                </div>
            </div>
        ';
        exit;
    }
    if(empty($_POST['periode_akhir'])){
        echo '
            <div class="row align-items-center g-4">
                <div class="col-12 text-center text-danger">
                    Periode Akhir Tidak Boleh Kosong!
                </div>
            </div>
        ';
        exit;
    }

    // Buat Variabel
    $periode_awal = trim($_POST['periode_awal'] ?? '');
    $periode_akhir = trim($_POST['periode_akhir'] ?? '');

    // Validasi period
    $periode_awal_obj  = DateTime::createFromFormat('Y-m-d', $periode_awal);
    $periode_akhir_obj = DateTime::createFromFormat('Y-m-d', $periode_akhir);

    if (!$periode_awal_obj || $periode_awal_obj->format('Y-m-d') !== $periode_awal || !$periode_akhir_obj || $periode_akhir_obj->format('Y-m-d') !== $periode_akhir) {
        echo '
            <div class="row align-items-center g-4">
                <div class="col-12 text-center text-danger">
                    Format periode tidak valid.
                </div>
            </div>
        ';
        exit;
    }

    if ($periode_akhir_obj < $periode_awal_obj) {
        echo '
            <div class="row align-items-center g-4">
                <div class="col-12 text-center text-danger">
                    Periode Data Tidak Valid
                </div>
            </div>
        ';
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
        echo '
            <div class="row align-items-center g-4">
                <div class="col-12 text-center text-danger">
                    Gagal mempersiapkan query data.
                </div>
            </div>
        ';
        exit;
    }

    $stmt->bind_param('ss', $periode_awal_query, $periode_akhir_query);
    if (!$stmt->execute()) {
        echo '
            <div class="row align-items-center g-4">
                <div class="col-12 text-center text-danger">
                    Gagal mengambil data responden.
                </div>
            </div>
        ';
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
?>

<div class="row align-items-center g-4 mb-3">
    <div class="col-12 text-center">
        <b> Laporan Deskripsi Responden</b><br>
        <small>Periode <?php echo "<b>$periode_awal_format</b> - <b>$periode_akhir_format</b>"; ?></small>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-striped table-hover table-bordered table-sm">
                <thead>
                    <tr>
                        <td colspan="4">
                            <b>A. Distribusi Responden Berdasarkan Gender</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center"><small><b>No</b></small></td>
                        <td><small><b>Gender</b></small></td>
                        <td class="text-center"><small><b>Jumlah</b></small></td>
                        <td class="text-center"><small><b>Persentase</b></small></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center"><small>1</small></td>
                        <td class="text-left"><small>Laki-laki</small></td>
                        <td class="text-center"><small><?php echo $genderMale; ?></small></td>
                        <td class="text-center"><small><?php echo $genderMalePercent; ?> %</small></td>
                    </tr>
                    <tr>
                        <td class="text-center"><small>2</small></td>
                        <td class="text-left"><small>Perempuan</small></td>
                        <td class="text-center"><small><?php echo $genderFemale; ?></small></td>
                        <td class="text-center"><small><?php echo $genderFemalePercent; ?> %</small></td>
                    </tr>
                    <tr>
                        <td class="text-center" colspan="2">
                            <small><b>TOTAL</b></small>
                        </td>
                        <td class="text-center"><small><?php echo $totalGender; ?></small></td>
                        <td class="text-center"><small><?php echo $totalPercent; ?> %</small></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-striped table-hover table-bordered table-sm">
                <thead>
                    <tr>
                        <td colspan="4">
                            <b>B. Distribusi Responden Berdasarkan Tujuan Kunjungan</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center"><small><b>No</b></small></td>
                        <td><small><b>Kunjungan</b></small></td>
                        <td class="text-center"><small><b>Jumlah</b></small></td>
                        <td class="text-center"><small><b>Persentase</b></small></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center"><small>1</small></td>
                        <td class="text-left"><small>Rawat Jalan</small></td>
                        <td class="text-center"><small><?php echo $jumlahRajal; ?></small></td>
                        <td class="text-center"><small><?php echo $tujuanRajalPercent; ?> %</small></td>
                    </tr>
                    <tr>
                        <td class="text-center"><small>2</small></td>
                        <td class="text-left"><small>Rawat Inap</small></td>
                        <td class="text-center"><small><?php echo $jumlahRanap; ?></small></td>
                        <td class="text-center"><small><?php echo $tujuanRanapPercent; ?> %</small></td>
                    </tr>
                    <tr>
                        <td class="text-center" colspan="2">
                            <small><b>TOTAL</b></small>
                        </td>
                        <td class="text-center"><small><?php echo $totalTujuan; ?></small></td>
                        <td class="text-center"><small><?php echo $totalPercent; ?> %</small></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="table table-responsive">
            <table class="table table-striped table-hover table-bordered table-sm">
                <thead>
                    <tr>
                        <td colspan="4">
                            <b>C. Distribusi Responden Berdasarkan Usia</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center"><small><b>No</b></small></td>
                        <td><small><b>Klasifikasi Usia</b></small></td>
                        <td class="text-center"><small><b>Jumlah</b></small></td>
                        <td class="text-center"><small><b>Persentase</b></small></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center"><small>1</small></td>
                        <td class="text-left"><small>Remaja (&lt; 20)</small></td>
                        <td class="text-center"><small><?php echo $jumlahRemaja; ?></small></td>
                        <td class="text-center"><small><?php echo $remajaPercent; ?> %</small></td>
                    </tr>
                    <tr>
                        <td class="text-center"><small>2</small></td>
                        <td class="text-left"><small>Dewasa Awal (20 - 40)</small></td>
                        <td class="text-center"><small><?php echo $jumlahDewasaAwal; ?></small></td>
                        <td class="text-center"><small><?php echo $dewasaAwalPercent; ?> %</small></td>
                    </tr>
                    <tr>
                        <td class="text-center"><small>3</small></td>
                        <td class="text-left"><small>Dewasa Akhir (40 - 60)</small></td>
                        <td class="text-center"><small><?php echo $jumlahDewasaAkhir; ?></small></td>
                        <td class="text-center"><small><?php echo $dewasaAkhirPercent; ?> %</small></td>
                    </tr>
                    <tr>
                        <td class="text-center"><small>4</small></td>
                        <td class="text-left"><small>Lansia (&gt;= 60)</small></td>
                        <td class="text-center"><small><?php echo $jumlahLansia; ?></small></td>
                        <td class="text-center"><small><?php echo $lansiaPercent; ?> %</small></td>
                    </tr>
                    <tr>
                        <td class="text-center" colspan="2">
                            <small><b>TOTAL</b></small>
                        </td>
                        <td class="text-center"><small><?php echo $totalUsia; ?></small></td>
                        <td class="text-center"><small><?php echo $totalPercent; ?> %</small></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-12">
        <a href="_Page/DeskripsiResponden/ProsesCetak.php?periode_awal=<?php echo $periode_awal; ?>&periode_akhir=<?php echo $periode_akhir; ?>" class="btn btn-md btn-primary btn-rounded w-100" target="_blank">
            <i class="bi bi-file-pdf"></i> Cetak
        </a>
    </div>
</div>

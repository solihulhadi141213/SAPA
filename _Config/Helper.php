<?php
    function generateUUIDv4() {
        $data = openssl_random_pseudo_bytes(16);
        
        // Set versi 4
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set variant RFC 4122
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    //Special Captcha
    function GenerateCaptcha($length) {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Menghindari karakter ambigu
        $captcha = '';
        for ($i = 0; $i < $length; $i++) {
            $captcha .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $captcha;
    }
    
    //Membuat Token
    function GenerateToken($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        $charLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charLength - 1)];
        }
        return $randomString;
    }

    //Membuat Randome String
    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        $charLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charLength - 1)];
        }
        return $randomString;
    }

    //Membersihkan Variabel
    function validateAndSanitizeInput($input) {
        // Menghapus karakter yang tidak diinginkan
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input);
        $input = addslashes($input);
        return $input;
    }

    //Data Detail
    function GetDetailData($Conn, $Tabel, $Param, $Value, $Colom) {
        // Validasi input yang diperlukan
        if (empty($Conn)) {
            return "No Database Connection";
        }
        if (empty($Tabel)) {
            return "No Table Selected";
        }
        if (empty($Param)) {
            return "No Parameter Selected";
        }
        if (empty($Value)) {
            return "No Value Provided";
        }
        if (empty($Colom)) {
            return "No Column Selected";
        }
    
        // Escape table name and column name untuk mencegah SQL Injection
        $Tabel = mysqli_real_escape_string($Conn, $Tabel);
        $Param = mysqli_real_escape_string($Conn, $Param);
        $Colom = mysqli_real_escape_string($Conn, $Colom);
    
        // Menggunakan prepared statement
        $Qry = $Conn->prepare("SELECT $Colom FROM $Tabel WHERE $Param = ?");
        if ($Qry === false) {
            return "Query Preparation Failed: " . $Conn->error;
        }
    
        // Bind parameter
        $Qry->bind_param("s", $Value);
    
        // Eksekusi query
        if (!$Qry->execute()) {
            return "Query Execution Failed: " . $Qry->error;
        }
    
        // Mengambil hasil
        $Result = $Qry->get_result();
        $Data = $Result->fetch_assoc();
    
        // Menutup statement
        $Qry->close();
    
        // Mengembalikan hasil
        if (empty($Data[$Colom])) {
            return "";
        } else {
            return $Data[$Colom];
        }
    }

    /**
     * =====================================================
     * FUNCTION : GetSimrsToken
     * TUJUAN   : Mengambil token SIMRS aktif
     *            - Auto generate jika kosong / expired
     *            - Simpan token & expired ke database
     * PARAM    : $Conn (mysqli connection)
     * RETURN   : string token
     * =====================================================
     */
    function GetSimrsToken($Conn){
        date_default_timezone_set("Asia/Jakarta");

        // ===============================
        // AMBIL KONEKSI SIMRS AKTIF
        // ===============================
        $status = 1;

        $Qry = $Conn->prepare("SELECT * FROM setting_simrs WHERE status = ? LIMIT 1");
        $Qry->bind_param("i", $status);
        $Qry->execute();
        $Result = $Qry->get_result();

        if ($Result->num_rows == 0) {
            $Qry->close();
            return false;
        }

        $Data = $Result->fetch_assoc();
        $Qry->close();

        // ===============================
        // VARIABEL
        // ===============================
        $id_setting_simrs = $Data['id_setting_simrs'];
        $url_simrs        = rtrim($Data['url_simrs'], '/');
        $client_id        = $Data['client_id'];
        $client_key       = $Data['client_key'];
        $token            = $Data['token'];
        $datetime_expired = $Data['datetime_expired'];

        $now = date('Y-m-d H:i:s');

        // ===============================
        // CEK TOKEN PERLU DIPERBARUI?
        // ===============================
        $needNewToken = false;

        if (empty($token)) {
            $needNewToken = true;
        }

        if (!empty($datetime_expired) && strtotime($datetime_expired) <= strtotime($now)) {
            $needNewToken = true;
        }

        // ===============================
        // REQUEST TOKEN BARU
        // ===============================
        if ($needNewToken === true) {

            $payload = json_encode([
                "client_id"  => $client_id,
                "client_key" => $client_key
            ]);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url_simrs . "/API/SIMRS/get_token.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json'
                ],
                CURLOPT_TIMEOUT => 15
            ]);

            $response = curl_exec($curl);

            if ($response === false) {
                curl_close($curl);
                return false;
            }

            curl_close($curl);

            $res = json_decode($response, true);

            if (
                empty($res['response']['code']) ||
                $res['response']['code'] != 200 ||
                empty($res['metadata']['token'])
            ) {
                return false;
            }

            // ===============================
            // SIMPAN TOKEN BARU
            // ===============================
            $token            = $res['metadata']['token'];
            $datetime_expired = $res['metadata']['datetime_expired'];

            $Upd = $Conn->prepare("
                UPDATE setting_simrs 
                SET token = ?, datetime_expired = ?
                WHERE id_setting_simrs = ?
            ");
            $Upd->bind_param(
                "ssi",
                $token,
                $datetime_expired,
                $id_setting_simrs
            );
            $Upd->execute();
            $Upd->close();
        }

        // ===============================
        // TOKEN SIAP DIGUNAKAN
        // ===============================
        return $token;
    }

?>
<?php
include '../koneksi.php';

function generateUniqueCode($length, $conn, $column, $table, $type = 'alphanumeric') {
    $characters = '';
    switch ($type) {
        case 'alphanumeric':
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
        case 'uppercase':
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            break;
        case 'numeric':
            $characters = '0123456789';
            break;
    }

    $maxIndex = strlen($characters) - 1;

    do {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, $maxIndex)];
        }

        if ($type === 'numeric' && $length === 3 && (int)$code > 500) {
            continue;
        }

        $query = "SELECT COUNT(*) as count FROM $table WHERE $column = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
    } while (isset($row) && $row['count'] > 0);

    return $code;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['dataPembelian'])) {
        $dataPembelian = htmlspecialchars($_POST['dataPembelian']);

        $data = explode('|', $dataPembelian);
        if (count($data) === 6) {
            $id_user = trim($data[0]);
            $id_event = trim($data[1]);
            $id_day = trim($data[2]);
            $id_presale = trim($data[3]);
            $jumlah_tiket = trim($data[4]);
            $total = trim($data[5]);

            $kode_ticket = generateUniqueCode(7, $conn, 'kode_ticket', 'purchase_user', 'uppercase');
            // $kode_unik = (int)generateUniqueCode(3, $conn, 'kode_unik', 'purchase_user', 'numeric');
            do {
                $kode_unik = (int)generateUniqueCode(3, $conn, 'kode_unik', 'purchase_user', 'numeric');
            } while ($kode_unik > 500);
            
            $total_akhir = $total + $kode_unik;
            $ref_id = generateUniqueCode(15, $conn, 'ref_id', 'purchase_user', 'alphanumeric');

            $sql = "INSERT INTO purchase_user 
                    (id_user, id_event, id_day, id_presale, kode_ticket, jumlah_tiket, total, kode_unik, total_akhir, ref_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("iiiisidsds", 
                $id_user, $id_event, $id_day, $id_presale, $kode_ticket, $jumlah_tiket, $total, $kode_unik, $total_akhir, $ref_id);

                if ($stmt->execute()) {
                    echo "<script>
                        alert('Pemesanan tiket berhasil! Kode Ticket: $kode_ticket');
                        window.location.href = '../payment-page.php?kode_ticket=$kode_ticket';
                    </script>";
                    exit();
                } else {
                    echo "<script>
                        alert('Terjadi kesalahan saat menyimpan: {$stmt->error}');
                        window.history.back();
                    </script>";
                    exit();
                }

                $stmt->close();
            } else {
                echo "<script>
                    alert('Gagal menyiapkan query: {$conn->error}');
                    window.history.back();
                </script>";
                exit();
            }
        } else {
            echo "<script>
                alert('Pemesanan tiket tidak valid! Data yang diterima: " . json_encode($data) . "');
                window.history.back();
            </script>";
            exit();
        }
    } else {
        echo "<script>
            alert('Data pemesanan tiket tidak ditemukan!');
            window.history.back();
        </script>";
        exit();
    }
} else {
    echo "<script>
        alert('Metode tidak valid!');
        window.history.back();
    </script>";
    exit();
}

$conn->close();
?>
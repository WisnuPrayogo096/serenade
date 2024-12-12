<?php
require_once realpath(__DIR__ . '/../../koneksi.php');

$query = "
    SELECT 
        p.id, 
        e.nama_event, 
        d.no_day, 
        p.no_presale, 
        p.harga_ticket, 
        p.kuota_ticket 
    FROM presale_ticket p
    JOIN event_konser e ON p.id_event = e.id
    JOIN day_event d ON p.id_day = d.id
    ORDER BY p.id ASC
";

$result = $conn->query($query);

$presaleTickets = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $presaleTickets[] = $row;
    }
}

$conn->close();
?>
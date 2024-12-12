<?php
require_once realpath(__DIR__ . '/../../koneksi.php');

// Ambil parameter event_id dari URL
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

header('Content-Type: application/json');

if ($event_id > 0) {
    $stmt = $conn->prepare("SELECT id, no_day, tanggal_perform FROM day_event WHERE id_event = ? ORDER BY no_day ASC");
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $days = [];
    while ($row = $result->fetch_assoc()) {
        // Format tanggal menjadi 'hari, bulan (3 huruf), tahun'
        $formattedDate = date('d M Y', strtotime($row['tanggal_perform'])); // contoh: '10 Dec 2024'
        $days[] = [
            'id' => $row['id'],
            'no_day' => $row['no_day'],
            'tanggal_perform' => $formattedDate
        ];
    }

    echo json_encode($days);
} else {
    echo json_encode([]);
}
?>
<?php
require_once realpath(__DIR__ . '/../../koneksi.php');

$query = "SELECT id, nama_event FROM event_konser ORDER BY nama_event ASC";
$result = $conn->query($query);

// Periksa apakah data ada
if ($result->num_rows > 0) {
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
} else {
    $events = [];
}
?>
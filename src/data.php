<?php
$products = [
    "1" => [
        "name" => "Kopi Susu Aren 1 Liter",
        "price" => "Rp42.000",
        "category" => "Minuman",
        "stock" => 18,
        "image" => "☕",
        "description" => "Kopi susu aren creamy dengan rasa seimbang, cocok untuk teman meeting, kerja sore, atau stok kulkas di rumah."
    ],
    "2" => [
        "name" => "Keyboard Mechanical Sagara",
        "price" => "Rp329.000",
        "category" => "Aksesoris",
        "stock" => 7,
        "image" => "⌨️",
        "description" => "Keyboard mechanical dengan rasa ketik mantap untuk meja kerja yang rapi, fokus, dan nyaman dipakai lama."
    ],
    "3" => [
        "name" => "Mouse Wireless Orbit",
        "price" => "Rp119.000",
        "category" => "Aksesoris",
        "stock" => 21,
        "image" => "🖱️",
        "description" => "Mouse wireless ringan dengan genggaman nyaman untuk kerja harian, browsing santai, dan setup meja minimalis."
    ],
    "4" => [
        "name" => "USB WiFi Adapter Mini",
        "price" => "Rp89.000",
        "category" => "Network",
        "stock" => 12,
        "image" => "📡",
        "description" => "Adapter WiFi ringkas untuk backup koneksi di rumah, kos, atau meja kerja yang butuh perangkat serba praktis."
    ],
    "5" => [
        "name" => "Hoodie DevSecOps",
        "price" => "Rp185.000",
        "category" => "Fashion",
        "stock" => 5,
        "image" => "🧥",
        "description" => "Hoodie nyaman dengan potongan santai untuk ruang kerja dingin, perjalanan malam, atau sekadar outfit simpel."
    ],
    "6" => [
        "name" => "Notebook Incident Report",
        "price" => "Rp35.000",
        "category" => "Stationery",
        "stock" => 40,
        "image" => "📓",
        "description" => "Notebook compact untuk catatan cepat, ide harian, daftar tugas, atau teman rapat yang tetap enak dilihat."
    ],
];

function rupiah_to_number($price) {
    return (int) preg_replace('/[^0-9]/', '', $price);
}

function looks_like_sqli($value) {
    $patterns = [
        "/'/",
        "/%27/i",
        "/\bor\b/i",
        "/\band\b/i",
        "/\bunion\b/i",
        "/\bselect\b/i",
        "/--/",
        "/\/\*/",
        "/information_schema/i",
        "/sleep\s*\(/i"
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $value)) {
            return true;
        }
    }
    return false;
}
?>

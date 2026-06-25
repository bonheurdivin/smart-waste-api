<?php

require_once 'config/database.php';

$conn = getConnection();

echo "🌱 Seeding database...\n";

// =====================
// USERS
// =====================
$users = [
    ['Alice Uwase', '+250788222222', 'alice@example.com', 'resident'],
    ['Bob Mugabo', '+250788333333', 'bob@example.com', 'resident'],
    ['Claire Neza', '+250788444444', 'claire@example.com', 'resident'],
    ['David Habi', '+250788555555', 'david@example.com', 'resident'],
    ['Grace Uwim', '+250788666666', 'grace@example.com', 'resident'],
    ['Jane Smith', '+250788777777', 'jane@smartwaste.com', 'worker'],
    ['Peter K.', '+250788888888', 'peter@smartwaste.com', 'worker'],
    ['Dispatcher One', '+250788000001', 'dispatcher@smartwaste.com', 'dispatcher'],
    ['Finance One', '+250788000002', 'finance@smartwaste.com', 'finance'],
];

foreach ($users as $user) {
    $password = password_hash('password123', PASSWORD_BCRYPT);
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO users (name, phone, email, password, role)
        VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('sssss', $user[0], $user[1], $user[2], $password, $user[3]);
    $stmt->execute();
}
echo "✅ Users seeded\n";

// =====================
// WORKERS
// =====================
$workers = [
    [6, 'Zone B', 'active'],  // Jane Smith user_id
    [7, 'Zone C', 'active'],  // Peter K. user_id
];

foreach ($workers as $worker) {
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO workers (user_id, zone, status)
        VALUES (?, ?, ?)"
    );
    $stmt->bind_param('iss', $worker[0], $worker[1], $worker[2]);
    $stmt->execute();
}
echo "✅ Workers seeded\n";

// =====================
// VEHICLES
// =====================
$vehicles = [
    ['RAC 002 B', '3 Tons', 'available', 2],
    ['RAC 003 C', '5 Tons', 'in-use', 3],
    ['RAC 004 D', '3 Tons', 'maintenance', null],
];

foreach ($vehicles as $vehicle) {
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO vehicles (plate, capacity, status, assigned_driver_id)
        VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('sssi', $vehicle[0], $vehicle[1], $vehicle[2], $vehicle[3]);
    $stmt->execute();
}
echo "✅ Vehicles seeded\n";

// =====================
// HOUSEHOLDS
// =====================
$households = [
    [4, 'KN 45 St, Kigali', 'Zone B', -1.9500, 30.0600, 2, 3],
    [5, 'KK 78 St, Kigali', 'Zone C', -1.9600, 30.0700, 3, 2],
    [6, 'KG 56 St, Kigali', 'Zone A', -1.9400, 30.0500, 1, 4],
    [7, 'KN 90 St, Kigali', 'Zone B', -1.9550, 30.0650, 2, 2],
    [8, 'KK 12 St, Kigali', 'Zone C', -1.9650, 30.0750, 3, 3],
];

foreach ($households as $h) {
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO households
        (owner_user_id, address, zone, gps_lat, gps_lng, plan_id, occupants)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('issddii', $h[0], $h[1], $h[2], $h[3], $h[4], $h[5], $h[6]);
    $stmt->execute();
}
echo "✅ Households seeded\n";

// =====================
// SCHEDULES
// =====================
$schedules = [
    [2, 'Zone A', 'weekly', '2026-05-25 08:00:00'],
    [3, 'Zone B', 'bi-weekly', '2026-05-26 08:00:00'],
    [4, 'Zone C', 'weekly', '2026-05-27 08:00:00'],
    [5, 'Zone A', 'on-demand', '2026-05-28 08:00:00'],
    [6, 'Zone B', 'weekly', '2026-05-29 08:00:00'],
];

foreach ($schedules as $s) {
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO schedules
        (household_id, zone, recurrence, next_pickup_at)
        VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('isss', $s[0], $s[1], $s[2], $s[3]);
    $stmt->execute();
}
echo "✅ Schedules seeded\n";

// =====================
// PICKUPS
// =====================
$pickups = [
    [2, '2026-05-10 08:00:00', '2026-05-10 09:30:00', 1, 1, 'completed', 5],
    [3, '2026-05-10 09:00:00', '2026-05-10 10:30:00', 2, 2, 'completed', 4],
    [4, '2026-05-11 08:00:00', null, 1, 1, 'missed', null],
    [5, '2026-05-11 09:00:00', '2026-05-11 10:00:00', 3, 3, 'completed', 5],
    [6, '2026-05-12 08:00:00', '2026-05-12 09:00:00', 2, 2, 'completed', 4],
    [2, '2026-05-15 08:00:00', null, 1, 1, 'scheduled', null],
    [3, '2026-05-15 09:00:00', null, 2, 2, 'en-route', null],
];

foreach ($pickups as $p) {
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO pickups
        (household_id, scheduled_at, completed_at, worker_id, vehicle_id, status, rating)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('issiisi', $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6]);
    $stmt->execute();
}
echo "✅ Pickups seeded\n";

// =====================
// PAYMENTS
// =====================
$payments = [
    [2, 5000, 'mobile-money', 'MM001', 'paid', '2026-05-01 10:00:00'],
    [3, 3000, 'mobile-money', 'MM002', 'paid', '2026-05-01 11:00:00'],
    [4, 5000, 'cash', 'CASH001', 'paid', '2026-05-02 10:00:00'],
    [5, 3000, 'mobile-money', 'MM003', 'unpaid', null],
    [6, 5000, 'mobile-money', 'MM004', 'unpaid', null],
];

foreach ($payments as $p) {
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO payments
        (household_id, amount, method, reference, status, paid_at)
        VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('idssss', $p[0], $p[1], $p[2], $p[3], $p[4], $p[5]);
    $stmt->execute();
}
echo "✅ Payments seeded\n";

// =====================
// COMPLAINTS
// =====================
$complaints = [
    [2, 'missed-pickup', 'Worker did not show up for scheduled pickup', 'open'],
    [3, 'worker-behaviour', 'Worker was rude during pickup', 'resolved'],
    [4, 'damage', 'Worker damaged our gate during pickup', 'open'],
    [5, 'missed-pickup', 'Pickup was missed without notice', 'resolved'],
];

foreach ($complaints as $c) {
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO complaints
        (household_id, type, description, status)
        VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('isss', $c[0], $c[1], $c[2], $c[3]);
    $stmt->execute();
}
echo "✅ Complaints seeded\n";

// =====================
// NOTIFICATIONS
// =====================
$notifications = [
    [1, 'push', 'Your pickup is scheduled for tomorrow at 8:00 AM', 'sent'],
    [1, 'push', 'Your worker John Doe is on the way', 'sent'],
    [4, 'sms', 'Your pickup has been completed', 'sent'],
    [5, 'push', 'Payment reminder: Your May invoice is pending', 'sent'],
    [6, 'push', 'Your pickup is scheduled for tomorrow at 8:00 AM', 'sent'],
];

foreach ($notifications as $n) {
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO notifications
        (recipient_user_id, channel, payload, status, sent_at)
        VALUES (?, ?, ?, ?, ?)"
    );
    $sentAt = date('Y-m-d H:i:s');
    $stmt->bind_param('issss', $n[0], $n[1], $n[2], $n[3], $sentAt);
    $stmt->execute();
}
echo "✅ Notifications seeded\n";

echo "\n🎉 Database seeded successfully!\n";
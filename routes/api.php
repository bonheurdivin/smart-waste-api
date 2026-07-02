<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'app/controllers/AuthController.php';

$conn = getConnection();
$authController = new AuthController($conn);

// Test route
if ($requestUri === '/api/v1' && $requestMethod === 'GET') {
    echo json_encode([
        'status' => 'success',
        'message' => 'Smart Waste API v1 is running!'
    ]);
    exit();
}

// Auth routes
if ($requestUri === '/api/v1/auth/register' && $requestMethod === 'POST') {
    $authController->register();
    exit();
}

if ($requestUri === '/api/v1/auth/login' && $requestMethod === 'POST') {
    $authController->login();
    exit();
}

if ($requestUri === '/api/v1/users/residents' && $requestMethod === 'GET') {
    $authController->getResidents();
    exit();
}

if ($requestUri === '/api/v1/users/workers' && $requestMethod === 'GET') {
    $authController->getWorkerUsers();
    exit();
}

// Load Household Controller
require_once 'app/controllers/HouseholdController.php';
$householdController = new HouseholdController($conn);

// Household routes
if ($requestUri === '/api/v1/households' && $requestMethod === 'GET') {
    $householdController->index();
    exit();
}

if ($requestUri === '/api/v1/households/my' && $requestMethod === 'GET') {
    $householdController->myHousehold();
    exit();
}

if ($requestUri === '/api/v1/households' && $requestMethod === 'POST') {
    $householdController->store();
    exit();
}

// Load Plan Controller
require_once 'app/controllers/PlanController.php';
$planController = new PlanController($conn);

// Plan routes
if ($requestUri === '/api/v1/plans' && $requestMethod === 'GET') {
    $planController->index();
    exit();
}

if ($requestUri === '/api/v1/plans' && $requestMethod === 'POST') {
    $planController->store();
    exit();
}

if (preg_match('/^\/api\/v1\/plans\/(\d+)$/', $requestUri, $matches)) {
    $id = $matches[1];
    if ($requestMethod === 'GET') {
        $planController->show($id);
        exit();
    }
    if ($requestMethod === 'PUT') {
        $planController->update($id);
        exit();
    }
    if ($requestMethod === 'DELETE') {
        $planController->destroy($id);
        exit();
    }
}

// Load Worker Controller
require_once 'app/controllers/WorkerController.php';
$workerController = new WorkerController($conn);

// Worker routes
if ($requestUri === '/api/v1/workers/register' && $requestMethod === 'POST') {
        $workerController->storeWithUser();
        exit();
}
if ($requestUri === '/api/v1/workers' && $requestMethod === 'GET') {
    $workerController->index();
    exit();
}

if ($requestUri === '/api/v1/workers' && $requestMethod === 'POST') {
    $workerController->store();
    exit();
}

if (preg_match('/^\/api\/v1\/workers\/(\d+)$/', $requestUri, $matches)) {
    $id = $matches[1];
    if ($requestMethod === 'GET') {
        $workerController->show($id);
        exit();
    }
    if ($requestMethod === 'PUT') {
        $workerController->update($id);
        exit();
    }
    if ($requestMethod === 'DELETE') {
        $workerController->destroy($id);
        exit();
    }
    
}

// Load Vehicle Controller
require_once 'app/controllers/VehicleController.php';
$vehicleController = new VehicleController($conn);

// Vehicle routes
if ($requestUri === '/api/v1/vehicles' && $requestMethod === 'GET') {
    $vehicleController->index();
    exit();
}

if ($requestUri === '/api/v1/vehicles' && $requestMethod === 'POST') {
    $vehicleController->store();
    exit();
}

if (preg_match('/^\/api\/v1\/vehicles\/(\d+)$/', $requestUri, $matches)) {
    $id = $matches[1];
    if ($requestMethod === 'GET') {
        $vehicleController->show($id);
        exit();
    }
    if ($requestMethod === 'PUT') {
        $vehicleController->update($id);
        exit();
    }
    if ($requestMethod === 'DELETE') {
        $vehicleController->destroy($id);
        exit();
    }
}

// Load Schedule Controller
require_once 'app/controllers/ScheduleController.php';
$scheduleController = new ScheduleController($conn);

// Schedule routes
if ($requestUri === '/api/v1/schedules' && $requestMethod === 'GET') {
    $scheduleController->index();
    exit();
}

if ($requestUri === '/api/v1/schedules' && $requestMethod === 'POST') {
    $scheduleController->store();
    exit();
}

if (preg_match('/^\/api\/v1\/schedules\/household\/(\d+)$/', $requestUri, $matches)) {
    $householdId = $matches[1];
    if ($requestMethod === 'GET') {
        $scheduleController->getByHousehold($householdId);
        exit();
    }
}

if (preg_match('/^\/api\/v1\/schedules\/(\d+)$/', $requestUri, $matches)) {
    $id = $matches[1];
    if ($requestMethod === 'GET') {
        $scheduleController->show($id);
        exit();
    }
    if ($requestMethod === 'PUT') {
        $scheduleController->update($id);
        exit();
    }
    if ($requestMethod === 'DELETE') {
        $scheduleController->destroy($id);
        exit();
    }
}

// Load Pickup Controller
require_once 'app/controllers/PickupController.php';
$pickupController = new PickupController($conn);

// Pickup routes
if ($requestUri === '/api/v1/pickups' && $requestMethod === 'GET') {
    $pickupController->index();
    exit();
}

if ($requestUri === '/api/v1/pickups' && $requestMethod === 'POST') {
    $pickupController->store();
    exit();
}

if (preg_match('/^\/api\/v1\/pickups\/household\/(\d+)$/', $requestUri, $matches)) {
    $householdId = $matches[1];
    if ($requestMethod === 'GET') {
        $pickupController->getByHousehold($householdId);
        exit();
    }
}

if (preg_match('/^\/api\/v1\/pickups\/(\d+)\/rate$/', $requestUri, $matches)) {
    $id = $matches[1];
    if ($requestMethod === 'POST') {
        $pickupController->rate($id);
        exit();
    }
}

if (preg_match('/^\/api\/v1\/pickups\/(\d+)$/', $requestUri, $matches)) {
    $id = $matches[1];
    if ($requestMethod === 'GET') {
        $pickupController->show($id);
        exit();
    }
    if ($requestMethod === 'PUT') {
        $pickupController->update($id);
        exit();
    }
    if ($requestMethod === 'DELETE') {
        $pickupController->destroy($id);
        exit();
    }
}

// Load Payment Controller
require_once 'app/controllers/PaymentController.php';
$paymentController = new PaymentController($conn);

// Payment routes
if ($requestUri === '/api/v1/payments' && $requestMethod === 'GET') {
    $paymentController->index();
    exit();
}

if ($requestUri === '/api/v1/payments' && $requestMethod === 'POST') {
    $paymentController->store();
    exit();
}

if (preg_match('/^\/api\/v1\/payments\/household\/(\d+)$/', $requestUri, $matches)) {
    $householdId = $matches[1];
    if ($requestMethod === 'GET') {
        $paymentController->getByHousehold($householdId);
        exit();
    }
}

if (preg_match('/^\/api\/v1\/payments\/(\d+)$/', $requestUri, $matches)) {
    $id = $matches[1];
    if ($requestMethod === 'GET') {
        $paymentController->show($id);
        exit();
    }
    if ($requestMethod === 'PUT') {
        $paymentController->update($id);
        exit();
    }
    if ($requestMethod === 'DELETE') {
        $paymentController->destroy($id);
        exit();
    }
}

// Load Complaint Controller
require_once 'app/controllers/ComplaintController.php';
$complaintController = new ComplaintController($conn);

// Complaint routes
if ($requestUri === '/api/v1/complaints' && $requestMethod === 'GET') {
    $complaintController->index();
    exit();
}

if ($requestUri === '/api/v1/complaints' && $requestMethod === 'POST') {
    $complaintController->store();
    exit();
}

if (preg_match('/^\/api\/v1\/complaints\/household\/(\d+)$/', $requestUri, $matches)) {
    $householdId = $matches[1];
    if ($requestMethod === 'GET') {
        $complaintController->getByHousehold($householdId);
        exit();
    }
}

if (preg_match('/^\/api\/v1\/complaints\/(\d+)$/', $requestUri, $matches)) {
    $id = $matches[1];
    if ($requestMethod === 'GET') {
        $complaintController->show($id);
        exit();
    }
    if ($requestMethod === 'PUT') {
        $complaintController->update($id);
        exit();
    }
    if ($requestMethod === 'DELETE') {
        $complaintController->destroy($id);
        exit();
    }
}

// Load Notification Controller
require_once 'app/controllers/NotificationController.php';
$notificationController = new NotificationController($conn);

// Notification routes
if ($requestUri === '/api/v1/notifications' && $requestMethod === 'GET') {
    $notificationController->index();
    exit();
}

if ($requestUri === '/api/v1/notifications/my' && $requestMethod === 'GET') {
    $notificationController->myNotifications();
    exit();
}

if ($requestUri === '/api/v1/notifications/send' && $requestMethod === 'POST') {
    $notificationController->send();
    exit();
}

// Load Report Controller
require_once 'app/controllers/ReportController.php';
$reportController = new ReportController($conn);

// Report routes
if ($requestUri === '/api/v1/reports/daily-pickups' && $requestMethod === 'GET') {
    $reportController->dailyPickups();
    exit();
}

if ($requestUri === '/api/v1/reports/monthly-revenue' && $requestMethod === 'GET') {
    $reportController->monthlyRevenue();
    exit();
}

if ($requestUri === '/api/v1/reports/worker-productivity' && $requestMethod === 'GET') {
    $reportController->workerProductivity();
    exit();
}

if ($requestUri === '/api/v1/reports/high-volume-zones' && $requestMethod === 'GET') {
    $reportController->highVolumeZones();
    exit();
}

if ($requestUri === '/api/v1/reports/dashboard' && $requestMethod === 'GET') {
    $reportController->dashboardSummary();
    exit();
}

if ($requestUri === '/api/v1/auth/change-password' && $requestMethod === 'POST') {
    $authController->changePassword();
    exit();
}

// Profile routes
if ($requestUri === '/api/v1/auth/profile' && $requestMethod === 'GET') {
    $authController->getMyProfile();
    exit();
}

if ($requestUri === '/api/v1/auth/profile/picture' && $requestMethod === 'POST') {
    $authController->updateProfilePicture();
    exit();
}

// Routes with ID parameter
if (preg_match('/^\/api\/v1\/households\/(\d+)$/', $requestUri, $matches)) {
    $id = $matches[1];
    if ($requestMethod === 'GET') {
        $householdController->show($id);
        exit();
    }
    if ($requestMethod === 'PUT') {
        $householdController->update($id);
        exit();
    }
    if ($requestMethod === 'DELETE') {
        $householdController->destroy($id);
        exit();
    }
}

// 404 handler
http_response_code(404);
echo json_encode([
    'status' => 'error',
    'message' => 'Route not found'
]);
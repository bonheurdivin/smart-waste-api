# Smart Waste Collection System — Backend API

A RESTful API built with PHP and MySQL that powers the Smart Waste Collection System platform, serving both the web admin panel and the mobile app.

## 🌐 Live API
https://smart-waste-collector.up.railway.app/api/v1

## 🛠️ Tech Stack
- PHP 8.2
- MySQL 8.x
- JWT Authentication (firebase/php-jwt)
- Docker (Railway deployment)
- Apache/PHP CLI Server

## ✨ Features
- JWT-based authentication with role-based access control
- Resident registration and household management
- Worker and vehicle fleet management
- Pickup scheduling and real-time tracking
- Payment tracking with mobile money confirmation flow
- Complaints management
- Push notifications logging
- Analytics and reports
- Profile picture upload
- Password management

## 👥 User Roles
| Role | Description |
|------|-------------|
| Admin | Full system access |
| Resident | Mobile app user |
| Worker | Field employee |

## 📡 API Endpoints

### Authentication
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/v1/auth/register` | Register resident | None |
| POST | `/api/v1/auth/login` | Login | None |
| POST | `/api/v1/auth/change-password` | Change password | Required |
| GET | `/api/v1/auth/profile` | Get my profile | Required |
| POST | `/api/v1/auth/profile/picture` | Update profile picture | Required |
| GET | `/api/v1/users/residents` | Get all residents | Admin |
| GET | `/api/v1/users/workers` | Get all worker users | Admin |

### Households
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/households` | Get all households | Admin |
| GET | `/api/v1/households/my` | Get my household | Resident |
| GET | `/api/v1/households/:id` | Get one household | Admin |
| POST | `/api/v1/households` | Create household | Admin/Resident |
| PUT | `/api/v1/households/:id` | Update household | Admin |
| DELETE | `/api/v1/households/:id` | Delete household | Admin |

### Workers
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/workers` | Get all workers | Admin |
| GET | `/api/v1/workers/:id` | Get one worker | Admin |
| POST | `/api/v1/workers` | Create worker profile | Admin |
| POST | `/api/v1/workers/register` | Register worker with account | Admin |
| PUT | `/api/v1/workers/:id` | Update worker | Admin |
| DELETE | `/api/v1/workers/:id` | Delete worker | Admin |

### Vehicles
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/vehicles` | Get all vehicles | Admin |
| GET | `/api/v1/vehicles/:id` | Get one vehicle | Admin |
| POST | `/api/v1/vehicles` | Create vehicle | Admin |
| PUT | `/api/v1/vehicles/:id` | Update vehicle | Admin |
| DELETE | `/api/v1/vehicles/:id` | Delete vehicle | Admin |

### Schedules
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/schedules` | Get all schedules | Admin |
| GET | `/api/v1/schedules/:id` | Get one schedule | Admin |
| GET | `/api/v1/schedules/household/:id` | Get by household | Admin/Resident |
| POST | `/api/v1/schedules` | Create schedule | Admin |
| PUT | `/api/v1/schedules/:id` | Update schedule | Admin |
| DELETE | `/api/v1/schedules/:id` | Delete schedule | Admin |

### Pickups
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/pickups` | Get all pickups | Admin |
| GET | `/api/v1/pickups/:id` | Get one pickup | Admin/Resident |
| GET | `/api/v1/pickups/household/:id` | Get by household | Admin/Resident |
| POST | `/api/v1/pickups` | Create pickup | Admin/Resident |
| PUT | `/api/v1/pickups/:id` | Update pickup | Admin |
| POST | `/api/v1/pickups/:id/rate` | Rate pickup | Resident |

### Payments
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/payments` | Get all payments | Admin |
| GET | `/api/v1/payments/:id` | Get one payment | Admin/Resident |
| GET | `/api/v1/payments/household/:id` | Get by household | Admin/Resident |
| POST | `/api/v1/payments` | Create payment | Admin/Resident |
| PUT | `/api/v1/payments/:id` | Update payment | Admin/Resident |
| DELETE | `/api/v1/payments/:id` | Delete payment | Admin |

### Complaints
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/complaints` | Get all complaints | Admin |
| GET | `/api/v1/complaints/:id` | Get one complaint | Admin/Resident |
| GET | `/api/v1/complaints/household/:id` | Get by household | Admin/Resident |
| POST | `/api/v1/complaints` | Submit complaint | Resident |
| PUT | `/api/v1/complaints/:id` | Update complaint | Admin |
| DELETE | `/api/v1/complaints/:id` | Delete complaint | Admin |

### Notifications
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/notifications` | Get all notifications | Admin |
| GET | `/api/v1/notifications/my` | Get my notifications | Resident |
| POST | `/api/v1/notifications/send` | Send notification | Admin |

### Plans
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/plans` | Get all plans | None |
| GET | `/api/v1/plans/:id` | Get one plan | None |
| POST | `/api/v1/plans` | Create plan | Admin |
| PUT | `/api/v1/plans/:id` | Update plan | Admin |
| DELETE | `/api/v1/plans/:id` | Delete plan | Admin |

### Reports
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/reports/dashboard` | Dashboard summary | Admin |
| GET | `/api/v1/reports/daily-pickups` | Daily pickups report | Admin |
| GET | `/api/v1/reports/monthly-revenue` | Monthly revenue | Admin |
| GET | `/api/v1/reports/worker-productivity` | Worker productivity | Admin |
| GET | `/api/v1/reports/high-volume-zones` | High volume zones | Admin |

## 🚀 Local Installation

### Requirements
- XAMPP (PHP 8.x + MySQL 8.x)
- Composer

### Steps
1. Clone the repository:
```bash
   git clone https://github.com/bonheurdivin/smart-waste-api.git
   cd smart-waste-api
```
2. Install dependencies:
```bash
   composer install
```
3. Create database in phpMyAdmin named `smart_waste_db`
4. Import `smart_waste_db.sql`
5. Update `config/database.php` with your credentials
6. Start XAMPP Apache and MySQL
7. Test at `http://localhost/smart-waste-api/api/v1`

## 📁 Project Structure
smart-waste-api/
├── app/
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── HouseholdController.php
│   │   ├── WorkerController.php
│   │   ├── VehicleController.php
│   │   ├── ScheduleController.php
│   │   ├── PickupController.php
│   │   ├── PaymentController.php
│   │   ├── ComplaintController.php
│   │   ├── NotificationController.php
│   │   └── ReportController.php
│   ├── models/
│   │   ├── User.php
│   │   ├── Household.php
│   │   ├── Worker.php
│   │   ├── Vehicle.php
│   │   ├── Schedule.php
│   │   ├── Pickup.php
│   │   ├── Payment.php
│   │   ├── Complaint.php
│   │   └── Notification.php
│   └── middleware/
│       ├── AuthMiddleware.php
│       └── RoleMiddleware.php
├── config/
│   └── database.php
├── routes/
│   └── api.php
├── uploads/
├── Dockerfile
├── router.php
├── .htaccess
├── index.php
└── README.md

## 🔐 Default Credentials
Admin Phone: +250788999999
Admin Password: admin123
Resident Phone: +250788123456
Resident Password: password123

## 👨‍💻 Author
**Divin** — Project 2026
- GitHub: [@bonheurdivin](https://github.com/bonheurdivin)

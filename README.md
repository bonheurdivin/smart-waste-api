# Smart Waste Collection System — Backend API

A RESTful API built with PHP and MySQL that powers the Smart Waste Collection System platform.

## Tech Stack
- PHP 8.x
- MySQL 8.x
- Apache (XAMPP)
- JWT Authentication
- Firebase PHP-JWT

## Features
- JWT-based authentication with role-based access control
- Household management
- Worker and vehicle fleet management
- Pickup scheduling and tracking
- Payment tracking
- Complaints management
- Push notifications
- Analytics and reports

## Roles
- **Admin** — Full access
- **Dispatcher** — Manages routes, workers and pickups
- **Finance** — Manages payments and reports
- **Resident** — Mobile app user
- **Worker** — Field employee

## Installation

### Requirements
- XAMPP (PHP 8.x + MySQL 8.x)
- Composer

### Steps
1. Clone the repository:
```bash
   git clone https://github.com/bonheurdivin/smart-waste-api.git
```
2. Move to XAMPP htdocs:
```bash
   mv smart-waste-api C:/xampp/htdocs/
```
3. Install dependencies:
```bash
   cd smart-waste-api
   composer install
```
4. Create database:
   - Open phpMyAdmin at `http://localhost/phpmyadmin`
   - Create database named `smart_waste_db`
   - Import `database.sql` file
5. Start XAMPP Apache and MySQL
6. Test API at `http://localhost/smart-waste-api/api/v1`

## API Endpoints

### Authentication
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/v1/auth/register` | Register resident | None |
| POST | `/api/v1/auth/login` | Login | None |
| POST | `/api/v1/auth/change-password` | Change password | Required |

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
| GET | `/api/v1/workers` | Get all workers | Admin/Dispatcher |
| GET | `/api/v1/workers/:id` | Get one worker | Admin/Dispatcher |
| POST | `/api/v1/workers` | Create worker | Admin |
| POST | `/api/v1/workers/register` | Register worker with account | Admin |
| PUT | `/api/v1/workers/:id` | Update worker | Admin |
| DELETE | `/api/v1/workers/:id` | Delete worker | Admin |

### Vehicles
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/vehicles` | Get all vehicles | Admin/Dispatcher |
| GET | `/api/v1/vehicles/:id` | Get one vehicle | Admin/Dispatcher |
| POST | `/api/v1/vehicles` | Create vehicle | Admin |
| PUT | `/api/v1/vehicles/:id` | Update vehicle | Admin |
| DELETE | `/api/v1/vehicles/:id` | Delete vehicle | Admin |

### Schedules
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/schedules` | Get all schedules | Admin/Dispatcher |
| GET | `/api/v1/schedules/:id` | Get one schedule | Admin/Dispatcher |
| GET | `/api/v1/schedules/household/:id` | Get by household | Admin/Resident |
| POST | `/api/v1/schedules` | Create schedule | Admin/Dispatcher |
| PUT | `/api/v1/schedules/:id` | Update schedule | Admin/Dispatcher |
| DELETE | `/api/v1/schedules/:id` | Delete schedule | Admin |

### Pickups
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/pickups` | Get all pickups | Admin/Dispatcher |
| GET | `/api/v1/pickups/:id` | Get one pickup | Admin/Resident |
| GET | `/api/v1/pickups/household/:id` | Get by household | Admin/Resident |
| POST | `/api/v1/pickups` | Create pickup | Admin/Dispatcher/Resident |
| PUT | `/api/v1/pickups/:id` | Update pickup | Admin/Dispatcher |
| POST | `/api/v1/pickups/:id/rate` | Rate pickup | Resident |

### Payments
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/payments` | Get all payments | Admin/Finance |
| GET | `/api/v1/payments/:id` | Get one payment | Admin/Finance/Resident |
| GET | `/api/v1/payments/household/:id` | Get by household | Admin/Resident |
| POST | `/api/v1/payments` | Create payment | Admin/Finance/Resident |
| PUT | `/api/v1/payments/:id` | Update payment | Admin/Finance/Resident |
| DELETE | `/api/v1/payments/:id` | Delete payment | Admin |

### Complaints
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/complaints` | Get all complaints | Admin/Dispatcher |
| GET | `/api/v1/complaints/:id` | Get one complaint | Admin/Resident |
| GET | `/api/v1/complaints/household/:id` | Get by household | Admin/Resident |
| POST | `/api/v1/complaints` | Submit complaint | Resident |
| PUT | `/api/v1/complaints/:id` | Update complaint | Admin/Dispatcher |
| DELETE | `/api/v1/complaints/:id` | Delete complaint | Admin |

### Notifications
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/notifications` | Get all notifications | Admin |
| GET | `/api/v1/notifications/my` | Get my notifications | Resident |
| POST | `/api/v1/notifications/send` | Send notification | Admin/Dispatcher |

### Reports
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/v1/reports/dashboard` | Dashboard summary | Admin |
| GET | `/api/v1/reports/daily-pickups` | Daily pickups | Admin |
| GET | `/api/v1/reports/monthly-revenue` | Monthly revenue | Admin/Finance |
| GET | `/api/v1/reports/worker-productivity` | Worker productivity | Admin/Dispatcher |
| GET | `/api/v1/reports/high-volume-zones` | High volume zones | Admin/Dispatcher |

## Project Structure

smart-waste-api/

├── app/

│   ├── controllers/

│   ├── models/

│   └── middleware/

├── config/

│   └── database.php

├── routes/

│   └── api.php

├── uploads/

├── vendor/

├── .htaccess

├── index.php

└── README.md

## Author
Divin — Internship Project 2026
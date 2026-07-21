# Tank Level SCADA Simulator

An open-source, production-ready industrial water tank SCADA & HMI simulator built on **Laravel 12**, **Tailwind CSS**, and **Vite**. 

This project simulates a real-time PLC-controlled water tank, providing telemetry dashboarding, multi-level safety alarms, operator control actions, detailed operational reports, and operator profile management. It is designed to serve as a reference architecture for SCADA/HMI web interfaces.

---

## ⚡ Features

### 🖥️ 1. Single-Page SCADA HMI Dashboard
* **Animated Tank Mimic**: Live fluid level, volume, dynamic flow rate directions, and colored threshold indicators.
* **Compact Viewport Design**: Designed to fit completely within a single viewport, ideal for control room monitor screens.
* **Process Controls**: Fully interactive control actions: `Start Filling`, `Stop Process`, `Reset Simulation`, and `Drain Tank`.
* **Automatic/Manual Mode**: Switch between closed-loop automation or manual control.
* **Process Analytics**: 4 real-time interactive trend charts tracking Level (%), Pressure (bar), Temperature (°C), and Flow Rate (L/min) via Chart.js.

### 🔔 2. Multi-Level Alarm Management Console
* **Four-Tier Safety Thresholds**: Evaluates live sensor inputs against:
  * **LLLL** (5% - Critical Low)
  * **Low** (15% - Warning Low)
  * **High** (80% - Warning High)
  * **HHHH** (95% - Critical High)
* **Operator Console**: Interactive panel to view active/historical alarms, acknowledge triggers, and clear active states.
* **Severity Counters**: Live count badges for Critical, High, Medium, and Low alarm severities.

### 📊 3. Reporting Hub & CSV Export
* **Operations Logging**: Automatic audit trail of operator and process actions.
* **Trend Analytics**: 24h tank level history graphs and alarm frequency charts.
* **One-Click Exports**: Export event logs, active/historical alarms, and trend logs directly to CSV or JSON formats.

### 👤 4. Access Control & Settings
* **Interactive Parameters**: Sliders to set fill/drain rates and real-time simulator speed modifiers.
* **Operational Configuration**: Form interface to modify tank capacity, sensor noise factors, units, and temperature/pressure safety bounds.
* **User Management**: Admin tool to manage operator profiles (Operator, Admin, Read-Only roles).

---

## 🛠️ Technology Stack

* **Backend**: Laravel 12 (PHP 8.3+)
* **Frontend**: HTML5, Vanilla JavaScript / Alpine.js, Tailwind CSS, Chart.js
* **Database & Cache**: SQLite / MySQL, Redis (for state management & caching)
* **DevOps**: Docker, Nginx, docker-compose

---

## 🚀 Quick Start

### Prerequisites
* PHP 8.3+
* Composer 2+
* Node.js 18+ & NPM
* Web Browser

### Setup Instructions

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-username/scada-tank-simulator.git
   cd scada-tank-simulator
   ```

2. **Install PHP & Frontend Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Migration & Seeding**
   ```bash
   # Initialize SQLite database
   touch database/database.sqlite
   
   # Run migrations and seed default operator/settings
   php artisan migrate --seed
   ```

5. **Build Assets & Start Dev Server**
   ```bash
   # Compile frontend assets
   npm run build
   
   # Start the Laravel backend
   php artisan serve
   ```
   *The application will now be available at `http://localhost:8000`*

6. **Start the Simulation Ticker Daemon**
   In a separate terminal tab, run the background simulation:
   ```bash
   php artisan scada:tick --loop --interval=1
   ```

### 🐳 Running with Docker
A production-ready multi-container architecture is included in `docker-compose.yml`:
```bash
docker-compose up --build
```
This runs:
* `app`: PHP 8.3-FPM production runtime
* `web`: Nginx reverse proxy serving static assets
* `mysql`: Persistent MySQL database
* `redis`: High-speed telemetry cache

---

## 📁 Repository Structure

```
├── app/
│   ├── Contracts/            # Abstraction for PLC/Simulation integrations
│   ├── Enums/                # AlarmPriority, ValveState, SystemStatus, etc.
│   ├── Http/Controllers/     # API/Web control route handlers
│   └── Services/             # SimulationEngine, AlarmManager, EventLogger
├── config/                   # Laravel configurations
├── database/                 # Migration files & Seeders
├── resources/                # Blade views, CSS, JS
├── routes/                   # Web & API Route declarations
├── Dockerfile                # Production multi-stage Docker build
├── nginx.conf                # Nginx proxy configuration
└── docker-compose.yml        # Docker Multi-service configuration
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:
1. Fork the project.
2. Create your feature branch (`git checkout -b feature/AmazingFeature`).
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the branch (`git push origin feature/AmazingFeature`).
5. Open a Pull Request.

---

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

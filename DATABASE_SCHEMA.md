# Database Schema

## Users Table
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('administrator', 'operator', 'viewer') DEFAULT 'viewer',
    remember_token VARCHAR(100),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Tanks Table
```sql
CREATE TABLE tanks (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    capacity_liters INT UNSIGNED DEFAULT 1000,
    level_percent DECIMAL(5,2) DEFAULT 35,
    temperature_c DECIMAL(5,2) DEFAULT 27.3,
    pressure_bar DECIMAL(5,2) DEFAULT 1.8,
    flow_rate_l_min DECIMAL(8,2) DEFAULT 18.4,
    operating_mode VARCHAR(50) DEFAULT 'auto',
    system_status VARCHAR(50) DEFAULT 'simulation',
    is_overflowing BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Pumps Table
```sql
CREATE TABLE pumps (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tank_id BIGINT,
    name VARCHAR(255) DEFAULT 'Main Pump',
    state VARCHAR(50) DEFAULT 'stopped',
    running_hours DECIMAL(10,3) DEFAULT 0,
    motor_current_a DECIMAL(5,2) DEFAULT 7.2,
    motor_temperature_c DECIMAL(5,2) DEFAULT 47.8,
    energy_used_kwh DECIMAL(10,3) DEFAULT 0,
    efficiency_percent DECIMAL(5,2) DEFAULT 86.1,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (tank_id) REFERENCES tanks(id) ON DELETE CASCADE
);
```

## Sensors Table
```sql
CREATE TABLE sensors (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tank_id BIGINT,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100) NOT NULL,
    unit VARCHAR(50),
    value DECIMAL(10,3) DEFAULT 0,
    min_value DECIMAL(10,3),
    max_value DECIMAL(10,3),
    status VARCHAR(50) DEFAULT 'normal',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (tank_id) REFERENCES tanks(id) ON DELETE CASCADE
);
```

## Alarms Table
```sql
CREATE TABLE alarms (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tank_id BIGINT,
    priority VARCHAR(50) DEFAULT 'low',
    message VARCHAR(255) NOT NULL,
    equipment VARCHAR(255),
    status VARCHAR(50) DEFAULT 'active',
    acknowledged_by VARCHAR(255),
    acknowledged_at TIMESTAMP NULL,
    triggered_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (tank_id) REFERENCES tanks(id) ON DELETE CASCADE
);
```

## Events Table
```sql
CREATE TABLE events (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tank_id BIGINT,
    type VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    meta JSON,
    operator_id BIGINT,
    occurred_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (tank_id) REFERENCES tanks(id) ON DELETE CASCADE,
    FOREIGN KEY (operator_id) REFERENCES users(id) ON DELETE SET NULL
);
```

## Pump Logs Table
```sql
CREATE TABLE pump_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tank_id BIGINT,
    pump_state VARCHAR(50) NOT NULL,
    motor_current_a DECIMAL(5,2) DEFAULT 0,
    motor_temperature_c DECIMAL(5,2) DEFAULT 0,
    energy_used_kwh DECIMAL(10,3) DEFAULT 0,
    logged_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (tank_id) REFERENCES tanks(id) ON DELETE CASCADE
);
```

## Trend Data Table
```sql
CREATE TABLE trend_data (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tank_level DECIMAL(5,2) DEFAULT 0,
    pressure DECIMAL(5,2) DEFAULT 0,
    temperature DECIMAL(5,2) DEFAULT 0,
    flow_rate DECIMAL(8,2) DEFAULT 0,
    motor_current DECIMAL(5,2) DEFAULT 0,
    energy_consumption DECIMAL(10,3) DEFAULT 0,
    recorded_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Settings Table
```sql
CREATE TABLE settings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    key VARCHAR(255) UNIQUE NOT NULL,
    value JSON,
    group VARCHAR(100) DEFAULT 'simulation',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Relationships

- **Users** → has many Events (as operator)
- **Tanks** → has many Pumps, Sensors, Alarms, Events
- **Pumps** → belongs to Tank
- **Sensors** → belongs to Tank
- **Alarms** → belongs to Tank
- **Events** → belongs to Tank, belongs to User (operator)

# Development Guide

## Architecture Overview

The SCADA Simulator uses a **service-oriented, data-provider pattern** to separate simulation logic from the presentation layer.

```
┌─────────────────────────────────────────────────────────────┐
│                    HTTP Requests (Browser)                   │
└────────────────────────────┬────────────────────────────────┘
                             │
                    ┌────────▼─────────┐
                    │   Controllers    │
                    │ DashboardCtrl    │
                    │ TankCtrl         │
                    │ AlarmCtrl        │
                    │ ReportCtrl       │
                    └────────┬─────────┘
                             │
                ┌────────────▼──────────────┐
                │  TankDataProvider        │
                │  (Interface)             │
                └─────┬──────────────┬─────┘
                      │              │
        ┌─────────────▼──┐      ┌────▼────────────┐
        │SimulationEngine│      │   PLCProvider   │
        │   (Simulation) │      │  (Modbus/OPC UA)│
        └─────────────┬──┘      └─────────────────┘
                      │
        ┌─────────────▼──────────────────┐
        │  Core Services                 │
        │ ├─ TankService                │
        │ ├─ PumpService                │
        │ ├─ SensorService              │
        │ ├─ AlarmManager               │
        │ ├─ EventLogger                │
        │ └─ TrendRecorder              │
        └─────────────┬──────────────────┘
                      │
        ┌─────────────▼──────────────────┐
        │   Data Layer (Eloquent)        │
        │ ├─ Users, Tanks, Alarms        │
        │ ├─ Events, TrendData           │
        │ └─ Settings                    │
        └────────────────────────────────┘
```

## Core Services

### SimulationEngine
**Location**: `app/Services/SimulationEngine.php`

Manages the main state machine:
- Maintains tank state in cache
- Advances simulation when `snapshot()` is called
- Handles process transitions (filling, draining, idle)
- Evaluates alarms based on thresholds

```php
// Advance simulation one step
$state = $engine->snapshot();

// Check current state
$state = $engine->state();

// Trigger actions
$engine->startFilling();
$engine->stopProcess();
$engine->reset();
```

### TankService
Calculates tank volumes and level conversions:

```php
$volume = $service->levelToVolume($levelPercent, $capacityLiters);
```

### PumpService
Simulates pump dynamics:
- Motor current variations
- Temperature changes
- Running hours accumulation
- Efficiency tracking

```php
$pump = $service->advance($pumpState, $process, $speed);
```

### SensorService
Generates realistic sensor readings with noise:

```php
$sensors = $service->snapshot($state);
$fluctuatedValue = $service->fluctuate($value, $min, $max, $noise);
```

### AlarmManager
Evaluates state thresholds and triggers alarms:

```php
$alarms = $manager->evaluate($state);
// Returns active alarms based on tank level
```

### EventLogger
Records all system events to the database:

```php
$logger->record('Pump Started', 'Process started filling.', ['action' => 'start']);
```

### TrendRecorder
Persists historical metrics:

```php
$recorder->record($state);
```

## Data Provider Pattern

### TankDataProvider Interface
**Location**: `app/Contracts/TankDataProvider.php`

This abstraction allows switching between simulation and real PLC data without changing controllers:

```php
interface TankDataProvider {
    public function snapshot(): array;
    public function startFilling(): array;
    public function stopProcess(): array;
    // ... other actions
}
```

### SimulationProvider
Uses `SimulationEngine` for testing and demos.

### PLCProvider
**To be implemented** - will connect to real hardware via:
- Modbus TCP
- OPC UA
- MQTT

## Extending the System

### Adding a New Sensor Type

1. **Update** `database/migrations/2026_07_21_000004_create_sensors_table.php`
2. **Create** a migration to add the column
3. **Update** `SensorService::snapshot()` to include the new sensor
4. **Update** `Blade` dashboard to display it

Example:
```php
// In SensorService
public function snapshot(array $state): array
{
    return [
        'level_sensor' => $state['tank']['level_percent'],
        'humidity_sensor' => $this->fluctuate(60, 40, 80, 2.5),  // NEW
        // ...
    ];
}
```

### Adding a New Alarm Type

1. **Update** `AlarmManager::evaluate()` to add the condition
2. **Update** `config/scada.php` with threshold if needed
3. **Dashboard** will automatically show it

Example:
```php
public function evaluate(array $state): array
{
    $alarms = [];
    
    if ($state['pump']['motor_temperature_c'] > 70) {
        $alarms[] = $this->alarm('high', 'HIGH MOTOR TEMPERATURE', 'Pump', 'active');
    }
    
    return $alarms;
}
```

### Creating a New Report

1. **Create** view: `resources/views/reports/custom.blade.php`
2. **Create** controller method in `ReportController`
3. **Add** route in `routes/web.php`
4. **Add** link in dashboard or reports page

Example:
```php
// ReportController
public function energyUsage(): View
{
    $data = TrendData::whereBetween('recorded_at', [
        now()->subDays(7),
        now()
    ])->get();
    
    return view('reports.energy', compact('data'));
}
```

### Integrating a Real PLC

1. **Install** the transport library (e.g., `phpmodbus/phpmodbus`)
2. **Create** a new transport class:
   ```php
   namespace App\Services\Transports;
   
   class ModbusTcpTransport
   {
       public function __construct(string $host, int $port = 502) { }
       
       public function readRegister(int $address): int { }
       public function writeRegister(int $address, int $value): void { }
   }
   ```
3. **Extend** `PLCProvider`:
   ```php
   public function snapshot(): array
   {
       return [
           'tank' => [
               'level_percent' => $this->transport->readRegister(0) / 10,
               'temperature_c' => $this->transport->readRegister(1) / 10,
               // ...
           ],
           // ...
       ];
   }
   ```
4. **Update** `AppServiceProvider` to use `PLCProvider`

## Testing

### Unit Tests
```bash
php artisan test
```

### Manual Testing
```bash
php artisan tinker

# Get snapshot
$provider = app(TankDataProvider::class);
$state = $provider->snapshot();
dd($state);

# Trigger actions
$provider->startFilling();
$state = $provider->snapshot();
```

### API Testing
```bash
# Terminal 1: Start server
php artisan serve

# Terminal 2: Test endpoints
curl -X GET http://localhost:8000/api/tank
curl -X POST http://localhost:8000/api/start
curl -X GET http://localhost:8000/api/alarms
```

## Performance Optimization

### Cache Configuration
The simulation engine uses Laravel's file cache by default:

```php
// config/cache.php
'stores' => [
    'file' => [
        'driver' => 'file',
        'path' => storage_path('framework/cache/data'),
    ],
    // For production, use Redis:
    // 'redis' => [
    //     'driver' => 'redis',
    //     'connection' => 'default',
    // ]
],
```

### Database Optimization
Create indexes on frequently queried columns:

```php
// In a migration
Schema::table('trend_data', function (Blueprint $table) {
    $table->index('recorded_at');
});

Schema::table('alarms', function (Blueprint $table) {
    $table->index(['tank_id', 'status']);
});
```

## Code Style

The project follows PSR-12 standards. Format code with:

```bash
composer run lint
# or
php artisan pint
```

## Security Considerations

- ✅ CSRF protection enabled
- ✅ Password hashing using bcrypt
- ✅ SQL injection prevention via Eloquent
- ⚠️ Add authentication middleware for production
- ⚠️ Configure CORS for API if needed
- ⚠️ Rate limiting for API endpoints

Example auth middleware:
```php
Route::middleware('auth')->group(function () {
    Route::post('/api/start', [TankController::class, 'start']);
    Route::post('/api/stop', [TankController::class, 'stop']);
});
```

## File Structure

```
app/
├── Contracts/           # Interfaces (TankDataProvider)
├── Enums/              # Enums (AlarmPriority, PumpState, etc)
├── Models/             # Eloquent models (User, Tank, etc)
├── Services/           # Business logic
│   ├── SimulationEngine.php
│   ├── SimulationProvider.php
│   ├── PLCProvider.php
│   └── *Service.php
├── Http/
│   ├── Controllers/     # Route handlers
│   ├── Middleware/      # HTTP middleware
│   └── Requests/        # Form validation
└── Providers/           # Service providers

config/                  # Configuration files
database/
├── migrations/          # Schema definitions
└── seeders/             # Initial data

resources/
├── css/                 # Tailwind styles
├── js/                  # Alpine.js scripts
└── views/               # Blade templates

routes/
├── api.php              # API routes
└── web.php              # Web routes

storage/
├── app/                 # Application storage
├── framework/           # Framework cache/sessions
└── logs/                # Application logs
```

## Useful Artisan Commands

```bash
# Model generation
php artisan make:model ModelName -m  # with migration

# Controller generation
php artisan make:controller ControllerName --model=ModelName

# View generation
php artisan make:view view-name

# Test generation
php artisan make:test TestName

# Database
php artisan migrate:refresh --seed
php artisan seed --class=DatabaseSeeder

# Cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Route debugging
php artisan route:list
php artisan route:list --path=api
```

## Next Steps

1. Implement Modbus/OPC UA transport classes
2. Add authentication and role-based access control
3. Create additional report types (PDF export)
4. Build historical playback feature
5. Add WebSocket support for real-time updates
6. Implement data export (CSV, Excel)
7. Add email alerting
8. Create mobile-responsive views

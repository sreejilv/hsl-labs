# Testing Guide

This project includes comprehensive testing infrastructure covering various scenarios including happy path, validation errors, unauthorized access, and concurrency cases.

## Test Structure

### Test Categories

1. **Feature Tests** - Test complete user workflows

    - `SalesOrderTest.php` - Sales order functionality
    - `PatientManagementTest.php` - Patient CRUD operations
    - `PurchaseOrderTest.php` - Purchase order lifecycle

2. **Unit Tests** - Test individual components
    - Model logic
    - Service classes
    - Utility functions

### Test Scenarios Covered

#### 1. Happy Path Tests

-   ✅ `staff_can_create_sales_order_for_assigned_doctors_patients`
-   ✅ `staff_can_create_recurring_order`
-   ✅ `surgeon_can_create_patient`
-   ✅ `surgeon_can_create_purchase_order`

#### 2. Validation Error Tests

-   ✅ `sales_order_validation_requires_valid_data`
-   ✅ `recurring_order_validation_enforces_duration_range`
-   ✅ `patient_creation_validates_required_fields`
-   ✅ `purchase_order_validation_requires_valid_data`

#### 3. Unauthorized Access Tests

-   ✅ `staff_cannot_create_order_for_other_doctors_patients`
-   ✅ `unauthenticated_user_cannot_access_sales_orders`
-   ✅ `staff_cannot_create_patients`
-   ✅ `surgeon_cannot_update_other_doctors_patients`

#### 4. Concurrency Tests

-   ✅ `concurrent_orders_handle_stock_correctly`
-   ✅ `concurrent_recurring_order_processing_is_safe`

#### 5. Business Logic Tests

-   ✅ `sales_order_calculates_total_correctly`
-   ✅ `recurring_order_calculates_monthly_dates_correctly`
-   ✅ `purchase_order_can_be_received_and_updates_stock`

## Running Tests

### Local Development

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/SalesOrderTest.php

# Run specific test method
php artisan test --filter=staff_can_create_sales_order_for_assigned_doctors_patients

# Run tests in parallel (faster)
php artisan test --parallel
```

### Docker Environment

```bash
# Run tests in Docker
docker-compose exec app php artisan test

# Run with coverage in Docker
docker-compose exec app php artisan test --coverage --min=70
```

## Test Data Management

### Factories

The project uses Laravel factories for generating test data:

-   `UserFactory` - Creates surgeons and staff users
-   `PatientFactory` - Creates patient records
-   `ProductFactory` - Creates medical products

### Test Base Class

The `TestCase` class provides helper methods:

```php
// Create test users
$surgeon = $this->createSurgeon(['email' => 'doctor@test.com']);
$staff = $this->createStaff($surgeon);

// Create test data
$patient = $this->createPatient($surgeon);
$product = $this->createProduct(['stock_quantity' => 100]);

// Assertion helpers
$this->assertAuthorizationError($response);
$this->assertValidationError($response, ['field1', 'field2']);
```

## Continuous Integration

### GitHub Actions Pipeline

The project includes a comprehensive CI pipeline (`.github/workflows/ci.yml`) that:

1. **Test Job**

    - Sets up MySQL and Redis services
    - Runs PHP 8.2 with all required extensions
    - Installs dependencies and builds assets
    - Runs database migrations and seeds
    - Executes code style checks (Laravel Pint)
    - Performs static analysis (PHPStan)
    - Runs test suite with coverage requirements (minimum 70%)

2. **Security Job**

    - Runs composer security audit
    - Checks for known vulnerabilities

3. **Build Job**

    - Creates optimized production build
    - Generates deployment artifacts

4. **Deploy Jobs**
    - Staging deployment for `develop` branch
    - Production deployment for `main` branch

### Pipeline Triggers

-   Push to `main` or `develop` branches
-   Pull requests to `main` or `develop`

## Coverage Requirements

-   **Minimum Coverage**: 70%
-   **Focus Areas**: Controllers, Models, Services
-   **Excluded**: Console commands, Providers (except core logic)

## Best Practices

### Writing Tests

1. **Use descriptive test names** - `test_` or `/** @test */`
2. **Follow AAA pattern** - Arrange, Act, Assert
3. **Test one thing per test** - Single responsibility
4. **Use factories** - Don't create data manually
5. **Clean database** - Use `RefreshDatabase` trait

### Test Organization

```php
/** @test */
public function user_can_perform_action_under_specific_conditions(): void
{
    // Arrange - Set up test data and conditions
    $user = $this->createSurgeon();
    $patient = $this->createPatient($user);

    // Act - Perform the action
    $response = $this->actingAs($user)
        ->post(route('action'), $data);

    // Assert - Verify the outcome
    $response->assertOk();
    $this->assertDatabaseHas('table', $expectedData);
}
```

### Authorization Testing

Always test that users can only access their own data:

```php
/** @test */
public function user_cannot_access_other_users_data(): void
{
    $user1 = $this->createSurgeon(['email' => 'user1@test.com']);
    $user2 = $this->createSurgeon(['email' => 'user2@test.com']);
    $resource = $this->createResource($user2);

    $response = $this->actingAs($user1)
        ->get(route('resource.show', $resource));

    $this->assertAuthorizationError($response);
}
```

### Concurrency Testing

Test race conditions and concurrent access:

```php
/** @test */
public function concurrent_operations_handle_conflicts_correctly(): void
{
    $product = $this->createProduct(['stock_quantity' => 10]);

    // Simulate concurrent requests
    $responses = collect(range(1, 5))->map(function () use ($product) {
        return $this->post(route('orders.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 3]]
        ]);
    });

    // Assert only valid orders were created
    $this->assertCount(3, SalesOrder::all()); // Only 3 orders for stock of 10
}
```

## Troubleshooting

### Common Issues

1. **Database locks** - Use `RefreshDatabase` trait
2. **Memory issues** - Increase memory limit for large test suites
3. **Flaky tests** - Ensure proper cleanup and isolation
4. **Slow tests** - Use parallel testing or optimize database queries

### Debug Tips

```bash
# Run single test with verbose output
php artisan test --filter=test_name -v

# Enable debug mode
APP_DEBUG=true php artisan test

# Check test coverage for specific file
php artisan test --coverage --filter=SalesOrderTest
```

## Metrics and Monitoring

The CI pipeline tracks:

-   Test execution time
-   Code coverage percentage
-   Static analysis issues
-   Security vulnerabilities
-   Build success rate

Coverage reports are uploaded to Codecov for detailed analysis and trend monitoring.

<?php

use App\Http\Middleware\LogActivity;
use App\Livewire\Rekening\ActivityLogCrud;
use App\Models\ActivityLog;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Admin');
});

function createActivityLog(array $overrides = []): ActivityLog
{
    return ActivityLog::create(array_merge([
        'user_id' => '1',
        'user_name' => 'Admin User',
        'guard' => 'web',
        'method' => 'GET',
        'url' => 'http://localhost/rekening/notes',
        'route_name' => 'rekening.notes',
        'status_code' => 200,
        'ip_address' => '8.8.8.8',
        'user_agent' => 'Mozilla/5.0 Test Agent',
        'referer' => 'http://localhost/dashboard',
        'duration_ms' => 42,
        'payload' => ['search' => 'abc'],
        'session_id' => 'session-test',
        'created_at' => now(),
    ], $overrides));
}

it('records requests with method, status, url, ip, and user', function () {
    $this->actingAs($this->admin)->get(route('rekening.notes'))->assertOk();

    $log = ActivityLog::latest('id')->first();

    expect($log)->not->toBeNull()
        ->and($log->method)->toBe('GET')
        ->and($log->status_code)->toBe(200)
        ->and($log->url)->toContain('/rekening/notes')
        ->and($log->route_name)->toBe('rekening.notes')
        ->and($log->user_id)->toBe((string) $this->admin->id)
        ->and($log->user_name)->toBe($this->admin->name)
        ->and($log->guard)->toBe('web')
        ->and($log->ip_address)->not->toBeEmpty()
        ->and($log->duration_ms)->toBeGreaterThanOrEqual(0);
});

it('records error statuses returned by the application', function () {
    Route::middleware('web')->get('/testing/abort404', fn () => abort(404));
    Route::middleware('web')->get('/testing/abort500', fn () => abort(500));

    $this->get('/testing/abort404')->assertNotFound();
    $this->get('/testing/abort500')->assertStatus(500);

    expect(ActivityLog::orderBy('id')->pluck('status_code')->all())->toBe([404, 500]);
});

it('records the exception when a failure reaches the middleware', function () {
    $middleware = new LogActivity;

    expect(fn () => $middleware->handle(
        Request::create('/testing/boom'),
        fn () => throw new RuntimeException('boom failure'),
    ))->toThrow(RuntimeException::class);

    $log = ActivityLog::latest('id')->first();

    expect($log->status_code)->toBe(500)
        ->and($log->error)->toContain('boom failure');
});

it('masks sensitive input in the recorded payload', function () {
    $this->post(route('login.store'), [
        'email' => 'someone@example.com',
        'password' => 'super-secret-password',
    ]);

    $log = ActivityLog::where('route_name', 'login.store')->latest('id')->first();

    expect($log->payload['password'])->toBe('[redacted]')
        ->and($log->payload['email'])->toBe('someone@example.com')
        ->and(json_encode($log->payload))->not->toContain('super-secret-password');
});

it('does not record ignored requests', function () {
    $this->get('/up');
    $this->actingAs($this->admin)->get(route('rekening.activities'))->assertOk();

    expect(ActivityLog::count())->toBe(0);
});

it('renders the activity log table for a super admin', function () {
    createActivityLog();
    createActivityLog([
        'method' => 'POST',
        'status_code' => 500,
        'url' => 'http://localhost/rekening/customers',
        'route_name' => 'rekening.customers',
        'ip_address' => '10.10.10.10',
    ]);

    Livewire::actingAs($this->admin)
        ->test(ActivityLogCrud::class)
        ->assertSuccessful()
        ->assertSee('8.8.8.8')
        ->assertSee('10.10.10.10')
        ->assertSee('200')
        ->assertSee('500')
        ->assertSee('rekening.customers');
});

it('filters rows by method, status class, and search term', function () {
    createActivityLog(['method' => 'GET', 'status_code' => 200, 'ip_address' => '1.1.1.1']);
    createActivityLog(['method' => 'POST', 'status_code' => 422, 'ip_address' => '2.2.2.2']);
    createActivityLog(['method' => 'DELETE', 'status_code' => 503, 'ip_address' => '3.3.3.3']);

    Livewire::actingAs($this->admin)
        ->test(ActivityLogCrud::class)
        ->set('filterMethod', 'POST')
        ->assertSee('2.2.2.2')
        ->assertDontSee('1.1.1.1')
        ->set('filterMethod', '')
        ->set('filterStatus', '5xx')
        ->assertSee('3.3.3.3')
        ->assertDontSee('2.2.2.2')
        ->set('filterStatus', '')
        ->set('search', '1.1.1.1')
        ->assertSee('1.1.1.1')
        ->assertDontSee('3.3.3.3');
});

it('shows request details including the user agent', function () {
    $log = createActivityLog([
        'user_agent' => 'Mozilla/5.0 (Detail Test)',
        'payload' => ['name' => 'Budi', 'password' => '[redacted]'],
    ]);

    Livewire::actingAs($this->admin)
        ->test(ActivityLogCrud::class)
        ->call('view', $log->id)
        ->assertSet('showViewModal', true)
        ->assertSee('Mozilla/5.0 (Detail Test)')
        ->assertSee('session-test')
        ->assertSee('Budi')
        ->call('closeViewModal')
        ->assertSet('showViewModal', false)
        ->assertSet('viewingLog', null);
});

it('denies access to users without the super admin role', function () {
    $staff = User::factory()->create();

    $this->actingAs($staff)->get(route('rekening.activities'))->assertForbidden();

    Livewire::actingAs($staff)
        ->test(ActivityLogCrud::class)
        ->assertForbidden();
});

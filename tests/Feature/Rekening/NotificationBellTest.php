<?php

use App\Models\Account;
use App\Models\Agent;
use App\Models\AgentNotification;
use App\Models\Card;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Shipment;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->agent = Agent::factory()->create();
    $this->customer = Customer::factory()->create();
});

describe('Account Observer', function () {
    it('creates notification when account is created', function () {
        $account = Account::factory()->create([
            'agent_id' => $this->agent->id,
            'customer_id' => $this->customer->id,
        ]);

        assertDatabaseHas('agent_notifications', [
            'agent_id' => $this->agent->id,
            'type' => 'account',
            'action' => 'created',
            'notifiable_type' => Account::class,
            'notifiable_id' => $account->id,
        ]);
    });

    it('creates notification when account is updated', function () {
        $account = Account::factory()->create([
            'agent_id' => $this->agent->id,
            'customer_id' => $this->customer->id,
            'status' => 'bermasalah',
        ]);

        AgentNotification::query()->delete();

        $account->update(['status' => 'aktif']);

        assertDatabaseHas('agent_notifications', [
            'agent_id' => $this->agent->id,
            'type' => 'account',
            'action' => 'updated',
        ]);
    });
});

describe('Card Observer', function () {
    it('creates notification when card is created', function () {
        $account = Account::factory()->create([
            'agent_id' => $this->agent->id,
            'customer_id' => $this->customer->id,
        ]);

        AgentNotification::query()->delete();

        $card = Card::factory()->create([
            'account_id' => $account->id,
        ]);

        assertDatabaseHas('agent_notifications', [
            'agent_id' => $this->agent->id,
            'type' => 'card',
            'action' => 'created',
            'notifiable_type' => Card::class,
            'notifiable_id' => $card->id,
        ]);
    });
});

describe('Shipment Observer', function () {
    it('creates notification when shipment is created', function () {
        $account = Account::factory()->create([
            'agent_id' => $this->agent->id,
            'customer_id' => $this->customer->id,
        ]);

        AgentNotification::query()->delete();

        $shipment = Shipment::factory()->create([
            'agent_id' => $this->agent->id,
            'account_id' => $account->id,
        ]);

        assertDatabaseHas('agent_notifications', [
            'agent_id' => $this->agent->id,
            'type' => 'shipment',
            'action' => 'created',
            'notifiable_type' => Shipment::class,
            'notifiable_id' => $shipment->id,
        ]);
    });

    it('creates notification when shipment status is updated', function () {
        $account = Account::factory()->create([
            'agent_id' => $this->agent->id,
            'customer_id' => $this->customer->id,
        ]);

        $shipment = Shipment::factory()->create([
            'agent_id' => $this->agent->id,
            'account_id' => $account->id,
            'status' => 'PROCESS',
        ]);

        AgentNotification::query()->delete();

        $shipment->update(['status' => 'OTW']);

        assertDatabaseHas('agent_notifications', [
            'agent_id' => $this->agent->id,
            'type' => 'shipment',
            'action' => 'updated',
        ]);
    });
});

describe('Complaint Observer', function () {
    it('creates notification when complaint is created', function () {
        $account = Account::factory()->create([
            'agent_id' => $this->agent->id,
            'customer_id' => $this->customer->id,
        ]);

        AgentNotification::query()->delete();

        $complaint = Complaint::factory()->create([
            'agent_id' => $this->agent->id,
            'customer_id' => $this->customer->id,
            'account_id' => $account->id,
        ]);

        assertDatabaseHas('agent_notifications', [
            'agent_id' => $this->agent->id,
            'type' => 'complaint',
            'action' => 'created',
            'notifiable_type' => Complaint::class,
            'notifiable_id' => $complaint->id,
        ]);
    });
});

describe('AgentNotification Model', function () {
    it('can mark notification as read', function () {
        $notification = AgentNotification::factory()->unread()->create([
            'agent_id' => $this->agent->id,
        ]);

        expect($notification->isRead())->toBeFalse();

        $notification->markAsRead();

        expect($notification->fresh()->isRead())->toBeTrue();
    });

    it('provides correct icon based on type', function () {
        $notification = AgentNotification::factory()->create([
            'agent_id' => $this->agent->id,
            'type' => 'account',
        ]);

        expect($notification->icon)->toBe('credit-card');
    });

    it('provides correct color based on type', function () {
        $notification = AgentNotification::factory()->create([
            'agent_id' => $this->agent->id,
            'type' => 'complaint',
        ]);

        expect($notification->color)->toBe('rose');
    });
});

describe('Cleanup Command', function () {
    it('deletes notifications older than retention period', function () {
        AgentNotification::factory()->create([
            'agent_id' => $this->agent->id,
            'created_at' => now()->subMonths(4),
        ]);

        AgentNotification::factory()->create([
            'agent_id' => $this->agent->id,
            'created_at' => now(),
        ]);

        artisan('notifications:cleanup --months=3')
            ->assertSuccessful();

        expect(AgentNotification::count())->toBe(1);
    });

    it('supports dry run mode', function () {
        AgentNotification::factory()->create([
            'agent_id' => $this->agent->id,
            'created_at' => now()->subMonths(4),
        ]);

        artisan('notifications:cleanup --months=3 --dry-run')
            ->assertSuccessful();

        expect(AgentNotification::count())->toBe(1);
    });
});

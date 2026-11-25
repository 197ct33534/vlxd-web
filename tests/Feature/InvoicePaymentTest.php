<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_moves_only_within_customer_and_recalculates_both_projects(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $source = Customer::create(['name' => 'Khách chuyển'])->projects()->create(['name' => 'Công trình cũ', 'total_invoice' => 6380000, 'total_debt' => 6380000]);
        $foreign = Customer::create(['name' => 'Khách khác'])->projects()->create(['name' => 'Công trình khác']);
        $invoice = $source->invoices()->create(['total_amount' => 6380000]);
        $action = route('invoices.move-project', $invoice);

        $this->patch($action, ['project_id' => $foreign->id])->assertSessionHasErrors('project_id');
        $this->patch($action, ['project_name' => 'Công trình đường 39'])->assertRedirect(route('invoices.show', $invoice));
        $destination = $invoice->fresh()->project;
        $this->assertEquals($source->customer_id, $destination->customer_id);
        $this->assertDatabaseHas('projects', ['id' => $destination->id, 'name' => 'Công trình đường 39', 'total_debt' => 6380000]);
        $this->assertDatabaseHas('projects', ['id' => $source->id, 'total_debt' => 0]);

        $payment = $destination->payments()->create(['amount' => 100000, 'payment_type' => 'tung_dot']);
        $invoice->paymentAllocations()->create(['payment_id' => $payment->id, 'amount' => 100000]);
        $this->patch($action, ['project_id' => $source->id])->assertSessionHasErrors('project_id');
        $this->assertEquals($destination->id, $invoice->fresh()->project_id);
    }

    public function test_mark_as_paid_records_only_the_balance_once_for_the_selected_invoice(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $project = Customer::create(['name' => 'Khách thanh toán'])->projects()->create(['name' => 'Công trình']);
        $other = $project->invoices()->create(['invoice_date' => '2026-11-15', 'total_amount' => 500000]);
        $invoice = $project->invoices()->create(['invoice_date' => '2026-11-16', 'total_amount' => 300000]);
        $advance = $project->payments()->create([
            'payment_date' => '2026-11-16',
            'amount' => 100000,
            'payment_type' => 'tung_dot',
        ]);
        $invoice->paymentAllocations()->create(['payment_id' => $advance->id, 'amount' => 100000]);

        $index = route('projects.invoices.index', $project);
        $action = route('invoices.pay', $invoice);
        $this->get($index)->assertOk()
            ->assertSee('Còn phải thu 200.000 ₫')
            ->assertSee('Xác nhận thu đủ hóa đơn #'.$invoice->id.'?')
            ->assertSee($action);

        $this->post($action)->assertRedirect($index);
        $this->post($action)->assertRedirect($index)->assertSessionHas('success', 'Hóa đơn này đã thu đủ.');
        $this->assertDatabaseCount('payments', 2);
        $payment = $project->payments()->latest('id')->firstOrFail();
        $this->assertEquals(200000, $payment->amount);
        $this->assertDatabaseHas('payment_allocations', [
            'payment_id' => $payment->id,
            'invoice_id' => $invoice->id,
            'amount' => 200000,
        ]);
        $this->assertDatabaseMissing('payment_allocations', ['invoice_id' => $other->id]);
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'total_paid' => 300000, 'total_debt' => 500000]);
        $this->get($index)->assertOk()->assertSee('Đã thu đủ')->assertDontSee($action);
    }
}

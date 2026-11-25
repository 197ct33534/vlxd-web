<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_debt_adjustment_changes_only_the_combined_balance(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $customer = Customer::create(['name' => 'Khách công nợ tổng']);
        $first = $customer->projects()->create(['name' => 'Công trình 1', 'total_invoice' => 300000, 'total_debt' => 300000]);
        $second = $customer->projects()->create(['name' => 'Công trình 2', 'total_invoice' => 200000, 'total_debt' => 200000]);
        $url = route('customers.projects.debt-adjustment.store', $customer);
        $payload = ['expected_debt' => 500000, 'debt_amount' => 400000, 'adjustment_reason' => 'Đối chiếu tổng'];
        $this->post($url, $payload)->assertRedirect(route('customers.projects.index', $customer));
        $this->post($url, $payload)->assertSessionHas('success', 'Công nợ tổng đã khớp với số tiền nhập.');
        $this->post($url, array_replace($payload, ['debt_amount' => 450000]))->assertSessionHasErrors('debt_amount');
        $this->post($url, array_replace($payload, ['expected_debt' => 400000, 'debt_amount' => 450000]))->assertSessionHas('success');

        $this->assertDatabaseHas('customer_advances', ['customer_id' => $customer->id, 'amount' => 100000, 'payment_method' => Payment::DEBT_ADJUSTMENT]);
        $this->assertDatabaseHas('customer_advances', ['customer_id' => $customer->id, 'amount' => -50000, 'payment_method' => Payment::DEBT_ADJUSTMENT]);
        $this->assertDatabaseCount('customer_advances', 2);
        $this->assertEquals(450000, Customer::withAccountTotals()->findOrFail($customer->id)->accountBalance());
        $this->assertEquals(0, Payment::receiptEntries()->sum('amount'));
        $this->assertDatabaseHas('projects', ['id' => $first->id, 'total_debt' => 300000]);
        $this->assertDatabaseHas('projects', ['id' => $second->id, 'total_debt' => 200000]);
        $this->get(route('customers.projects.index', $customer))->assertOk()
            ->assertSee('Cập nhật công nợ tổng')->assertSee('Điều chỉnh công nợ tổng')->assertSee('450.000 ₫');
    }

    public function test_customer_advance_is_unassigned_and_reduces_only_customer_balance(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $customer = Customer::create(['name' => 'Khách ứng tiền']);
        $first = $customer->projects()->create(['name' => 'Công trình 1', 'total_invoice' => 200000, 'total_debt' => 200000]);
        $second = $customer->projects()->create(['name' => 'Công trình 2', 'total_invoice' => 200000, 'total_debt' => 200000]);
        $foreign = Customer::create(['name' => 'Khách khác'])->projects()->create(['name' => 'Công trình khác']);
        $foreign->payments()->create(['amount' => 500, 'payment_type' => 'tung_dot', 'note' => 'Phiếu của khách khác']);
        $first->payments()->create(['amount' => 10000, 'payment_type' => 'tung_dot', 'note' => 'Phiếu riêng công trình']);
        $first->update(['total_paid' => 10000, 'total_debt' => 190000]);
        $url = route('customers.projects.advance.store', $customer);
        $data = [
            'amount' => 450000, 'payment_date' => '2026-11-25', 'payment_method' => 'bank_transfer',
            'note' => 'Khách ứng trước', 'advance_reference' => (string) \Illuminate\Support\Str::uuid(),
        ];

        $this->post($url, $data)->assertSessionHas('success', 'Đã ghi tiền ứng của khách hàng.');
        $this->post($url, $data)->assertSessionHas('success', 'Khoản ứng này đã được ghi.');
        $this->assertDatabaseCount('customer_advances', 1);
        $this->assertDatabaseHas('customer_advances', ['customer_id' => $customer->id, 'amount' => 450000]);
        $this->assertDatabaseCount('payments', 2);
        $this->assertDatabaseHas('projects', ['id' => $first->id, 'total_debt' => 190000]);
        $this->assertDatabaseHas('projects', ['id' => $second->id, 'total_debt' => 200000]);
        $this->assertDatabaseCount('payment_allocations', 0);
        $this->assertEquals(-60000, Customer::withAccountTotals()->findOrFail($customer->id)->accountBalance());
        $this->assertEquals(460500, Payment::receiptEntries()->sum('amount'));
        $this->assertEquals(0, Customer::totalOutstandingDebt());
        $this->get(route('customers.projects.index', $customer))->assertOk()
            ->assertSee('Lịch sử thanh toán')->assertSee('2 giao dịch')
            ->assertSee('Khoản ứng chung của khách hàng')->assertSee('Khách ứng trước')
            ->assertSee('Phiếu riêng công trình')->assertSee('Số dư trả trước')->assertSee('60.000 ₫')
            ->assertDontSee('Phiếu của khách khác');
        $this->get(route('customers.index', ['sort' => 'debt_desc']))->assertOk();
    }

    public function test_debt_adjustments_record_signed_differences_without_creating_receipts(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $project = Customer::create(['name' => 'Khách đối chiếu'])->projects()->create(['name' => 'Công trình đối chiếu']);
        $project->invoices()->create(['invoice_date' => '2026-11-15', 'total_amount' => 500000]);
        $project->payments()->create(['amount' => 100000, 'payment_type' => 'tung_dot']);
        $url = route('projects.debt-adjustments.store', $project);
        $reason = 'Đối chiếu sổ';

        $this->post($url, ['debt_amount' => 300000, 'expected_debt' => 400000, 'adjustment_reason' => $reason])->assertSessionHas('success');
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'total_debt' => 300000]);
        $this->assertDatabaseHas('payments', ['project_id' => $project->id, 'amount' => 100000, 'payment_method' => Payment::DEBT_ADJUSTMENT]);

        $this->post($url, ['debt_amount' => 420000, 'expected_debt' => 400000, 'adjustment_reason' => $reason])->assertSessionHasErrors('debt_amount');
        $request = ['debt_amount' => 420000, 'expected_debt' => 300000, 'adjustment_reason' => $reason];
        $this->post($url, $request)->assertSessionHas('success');
        $this->post($url, $request)->assertSessionHas('success', 'Công nợ đã khớp với số tiền nhập.');
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'total_debt' => 420000]);
        $this->assertDatabaseCount('payments', 3);
        $adjustment = $project->payments()->latest('id')->firstOrFail();
        $this->assertEquals(-120000, $adjustment->amount);
        $this->assertStringContainsString($reason, $adjustment->note);
        $this->assertEquals(100000, $project->payments()->receipts()->sum('amount'));
        $this->assertDatabaseCount('payment_allocations', 0);
        $this->put(route('payments.update', $adjustment))->assertForbidden();
        $this->delete(route('payments.destroy', $adjustment))->assertForbidden();
    }

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

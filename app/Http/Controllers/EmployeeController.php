<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::withSum('salaryAdvances', 'amount')->paginate(18);
        return view('employees.index', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'base_salary' => 'required|numeric|min:0',
            'avatar' => 'nullable|image|max:1024', // 1MB Max
        ]);

        $data = $request->except('avatar');

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        Employee::create($data);

        return redirect()->route('employees.index')->with('success', __('msg.employee_created'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'base_salary' => 'required|numeric|min:0',
            'avatar' => 'nullable|image|max:1024',
        ]);

        $data = $request->except('avatar');

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', __('msg.employee_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        if ($employee->avatar) {
            Storage::disk('public')->delete($employee->avatar);
        }
        $employee->delete();

        return redirect()->route('employees.index')->with('success', __('msg.employee_deleted'));
    }

    /**
     * Store a salary advance for the employee.
     */
    public function storeAdvance(Request $request, Employee $employee)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $employee->salaryAdvances()->create($request->all());

        return redirect()->route('employees.index')->with('success', __('msg.advance_recorded'));
    }

    /**
     * Get advances for an employee (JSON for modal).
     */
    public function getAdvances(Employee $employee)
    {
        return response()->json($employee->salaryAdvances()->latest()->get());
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Customer $customer)
    {
        // Eager load invoices if needed, or just paginate projects
        $projects = $customer->projects()->latest()->get();
        return view('projects.index', compact('customer', 'projects'));
    }

    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'end_date' => 'nullable|date',
        ]);

        $customer->projects()->create($validated);

        return redirect()->route('customers.projects.index', $customer->id)
            ->with('success', 'Project created successfully!');
    }

    public function update(Request $request, Customer $customer, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'end_date' => 'nullable|date',
        ]);

        $project->update($validated);

        return redirect()->route('customers.projects.index', $customer->id)
            ->with('success', 'Project updated successfully!');
    }

    public function destroy(Customer $customer, Project $project)
    {
        $project->delete();

        return redirect()->route('customers.projects.index', $customer->id)
            ->with('success', 'Project deleted successfully!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * All projects (quick access from sidebar; invoices still per project).
     */
    public function indexAll()
    {
        $projects = Project::with('customer')->latest()->paginate(20);

        return view('projects.all', compact('projects'));
    }

    public function index(Customer $customer)
    {
        // Eager load invoices if needed, or just paginate projects
        $projects = $customer->projects()->latest()->get();

        $agent = new \Jenssegers\Agent\Agent();
        if ($agent->isMobile()) {
            return view('projects.mobile_index', compact('customer', 'projects'));
        }

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
            ->with('success', __('msg.project_created'));
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
            ->with('success', __('msg.project_updated'));
    }

    public function destroy(Customer $customer, Project $project)
    {
        $project->delete();

        return redirect()->route('customers.projects.index', $customer->id)
            ->with('success', __('msg.project_deleted'));
    }
}

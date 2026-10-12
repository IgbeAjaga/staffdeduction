<?php

namespace App\Http\Controllers;

use App\Models\JobTitle;
use App\Http\Requests\StoreJobTitleRequest;
use App\Http\Requests\UpdateJobTitleRequest;

class JobTitleController extends Controller
{
    public function index()
    {
        $jobTitles = JobTitle::withTrashed()
            ->orderBy('name')
            ->paginate(20);

        return view('job-titles.index', compact('jobTitles'));
    }

    public function create()
    {
        return view('job-titles.create');
    }

    public function store(StoreJobTitleRequest $request)
    {
        JobTitle::create($request->validated());

        return redirect()
            ->route('job-titles.index')
            ->with('success', 'Job Title created successfully.');
    }

    public function show(JobTitle $jobTitle)
    {
        return view('job-titles.show', compact('jobTitle'));
    }

    public function edit(JobTitle $jobTitle)
    {
        return view('job-titles.edit', compact('jobTitle'));
    }

    public function update(UpdateJobTitleRequest $request, JobTitle $jobTitle)
    {
        $jobTitle->update($request->validated());

        return redirect()
            ->route('job-titles.index')
            ->with('success', 'Job Title updated successfully.');
    }

    public function destroy(JobTitle $jobTitle)
    {
        $jobTitle->delete();

        return redirect()
            ->route('job-titles.index')
            ->with('success', 'Job Title soft-deleted successfully.');
    }

    public function restore(int $id)
    {
        $jobTitle = JobTitle::withTrashed()->findOrFail($id);
        $jobTitle->restore();

        return redirect()
            ->route('job-titles.index')
            ->with('success', 'Job Title restored successfully.');
    }
}
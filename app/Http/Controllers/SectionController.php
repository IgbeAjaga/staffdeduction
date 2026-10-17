<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::withTrashed()
            ->orderBy('name')
            ->paginate(20);

        return view('sections.index', compact('sections'));
    }

    public function create()
    {
        return view('sections.create');
    }

    public function store(StoreSectionRequest $request)
    {
        Section::create($request->validated());

        return redirect()
            ->route('sections.index')
            ->with('success', 'Section created successfully.');
    }

    public function show(Section $section)
    {
        return view('sections.show', compact('section'));
    }

    public function edit(Section $section)
    {
        return view('sections.edit', compact('section'));
    }

    public function update(UpdateSectionRequest $request, Section $section)
    {
        $section->update($request->validated());

        return redirect()
            ->route('sections.index')
            ->with('success', 'Section updated successfully.');
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()
            ->route('sections.index')
            ->with('success', 'Section soft-deleted successfully.');
    }

    public function restore(int $id)
    {
        $section = Section::withTrashed()->findOrFail($id);
        $section->restore();

        return redirect()
            ->route('sections.index')
            ->with('success', 'Section restored successfully.');
    }
}
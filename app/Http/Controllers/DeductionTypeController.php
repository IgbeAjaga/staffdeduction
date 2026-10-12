<?php

namespace App\Http\Controllers;

use App\Models\DeductionType;
use App\Http\Requests\StoreDeductionTypeRequest;
use App\Http\Requests\UpdateDeductionTypeRequest;

class DeductionTypeController extends Controller
{
    public function index()
    {
        $deductionTypes = DeductionType::withTrashed()
            ->orderBy('name')
            ->paginate(20);

        return view('deduction-types.index', compact('deductionTypes'));
    }

    public function create()
    {
        return view('deduction-types.create');
    }

    public function store(StoreDeductionTypeRequest $request)
    {
        DeductionType::create($request->validated());

        return redirect()
            ->route('deduction-types.index')
            ->with('success', 'Deduction Type created successfully.');
    }

    public function show(DeductionType $deductionType)
    {
        return view('deduction-types.show', compact('deductionType'));
    }

    public function edit(DeductionType $deductionType)
    {
        return view('deduction-types.edit', compact('deductionType'));
    }

    public function update(UpdateDeductionTypeRequest $request, DeductionType $deductionType)
    {
        $deductionType->update($request->validated());

        return redirect()
            ->route('deduction-types.index')
            ->with('success', 'Deduction Type updated successfully.');
    }

    public function destroy(DeductionType $deductionType)
    {
        $deductionType->delete();

        return redirect()
            ->route('deduction-types.index')
            ->with('success', 'Deduction Type soft-deleted successfully.');
    }

    public function restore(int $id)
    {
        $deductionType = DeductionType::withTrashed()->findOrFail($id);
        $deductionType->restore();

        return redirect()
            ->route('deduction-types.index')
            ->with('success', 'Deduction Type restored successfully.');
    }
}
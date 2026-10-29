<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $employees = Employee::when($request->filled('q'), fn ($q) => $q->where('name', 'like', "%{$request->get('q')}%")->orWhere('email', 'like', "%{$request->get('q')}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('employee.index', compact('employees'));
    }

    public function create(): View
    {
        return view('employee.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:50'],
            'is_admin' => ['nullable'],
            'is_active' => ['nullable'],
        ]);

        $data['is_admin'] = $request->boolean('is_admin');
        $data['is_active'] = $request->boolean('is_active');

        Employee::create($data);

        return redirect()->route('admin.employees.index')->with('success', 'เพิ่มพนักงานเรียบร้อย');
    }

    public function edit(Employee $employee): View
    {
        return view('employee.form', compact('employee'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email,'.$employee->id],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:50'],
            'is_admin' => ['nullable'],
            'is_active' => ['nullable'],
        ]);

        $data['is_admin'] = $request->boolean('is_admin');
        $data['is_active'] = $request->boolean('is_active');

        if ($employee->id === Auth::guard('admin')->id() && ! $data['is_admin']) {
            return back()->with('error', 'ไม่สามารถถอดสิทธิ์เจ้าของร้านของตัวเองได้');
        }

        if ($employee->id === Auth::guard('admin')->id() && ! $data['is_active']) {
            return back()->with('error', 'ไม่สามารถระงับบัญชีของตัวเองได้');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $employee->update($data);

        return redirect()->route('admin.employees.index')->with('success', 'บันทึกพนักงานเรียบร้อย');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        if ($employee->id === Auth::guard('admin')->id()) {
            return back()->with('error', 'ไม่สามารถลบบัญชีตัวเองได้');
        }

        $employee->delete();

        return redirect()->route('admin.employees.index')->with('success', 'ลบพนักงานเรียบร้อย');
    }
}
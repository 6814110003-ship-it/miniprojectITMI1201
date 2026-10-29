@extends('layouts.admin')

@section('title', 'พนักงาน')
@section('content')

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-bold">พนักงาน</h2>
            <p class="text-sm text-gray-500">พบ {{ $employees->total() }} คน</p>
        </div>
        <a href="{{ route('admin.employees.create') }}" class="bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg px-4 py-2 text-sm transition">+ เพิ่มพนักงาน</a>
    </div>

    <form method="GET" action="{{ route('admin.employees.index') }}" class="mb-4 flex gap-2 max-w-md">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาชื่อ/อีเมล..."
               class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
        <button class="bg-gray-800 hover:bg-gray-900 text-white rounded-lg px-4 text-sm">ค้นหา</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">ชื่อ</th>
                    <th class="px-4 py-3 text-left">อีเมล</th>
                    <th class="px-4 py-3 text-left">ตำแหน่ง</th>
                    <th class="px-4 py-3 text-center">สิทธิ์</th>
                    <th class="px-4 py-3 text-center">สถานะ</th>
                    <th class="px-4 py-3 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($employees as $employee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $employee->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $employee->email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $employee->position ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($employee->is_admin)
                                <span class="text-xs font-bold px-2 py-1 rounded bg-sky-100 text-sky-700">เจ้าของ</span>
                            @else
                                <span class="text-xs font-bold px-2 py-1 rounded bg-gray-100 text-gray-600">พนักงาน</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($employee->is_active)
                                <span class="text-xs font-bold px-2 py-1 rounded bg-green-100 text-green-700">ใช้งาน</span>
                            @else
                                <span class="text-xs font-bold px-2 py-1 rounded bg-gray-200 text-gray-500">ระงับ</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <a href="{{ route('admin.employees.edit', $employee) }}" class="text-sky-600 hover:underline">แก้ไข</a>
                            <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('ลบนพักงานนี้?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline ml-2">ลบ</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">ยังไม่มีพนักงาน</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $employees->links() }}</div>

@endsection

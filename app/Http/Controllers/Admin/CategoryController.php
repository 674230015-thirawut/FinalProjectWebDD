<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('tickets')->orderBy('id')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

        return back()->with('success', 'เพิ่มหมวดหมู่งานซ่อมสำเร็จ');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return back()->with('success', 'อัปเดตข้อมูลหมวดหมู่เรียบร้อย');
    }

    public function destroy(Category $category)
    {
        if ($category->tickets()->count() > 0) {
            return back()->with('error', 'ไม่สามารถลบหมวดหมู่นี้ได้เนื่องจากมีใบแจ้งซ่อมที่ผูกอยู่');
        }

        $category->delete();

        return back()->with('success', 'ลบหมวดหมู่เรียบร้อย');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\FaqCategory;
use App\Models\FaqItem;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $categories = FaqCategory::with('items')->orderBy('order')->get();
        return view('faq.index', compact('categories'));
    }

    // Admin methods for categories
    public function createCategory()
    {
        return view('faq.create-category');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);

        FaqCategory::create([
            'name' => $request->name,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('faq.index')->with('success', 'Category created successfully.');
    }

    public function editCategory(FaqCategory $faqCategory)
    {
        return view('faq.edit-category', compact('faqCategory'));
    }

    public function updateCategory(Request $request, FaqCategory $faqCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $faqCategory->update([
            'name' => $request->name,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('faq.index')->with('success', 'Category updated successfully.');
    }

    public function destroyCategory(FaqCategory $faqCategory)
    {
        $faqCategory->delete();
        return redirect()->route('faq.index')->with('success', 'Category deleted successfully.');
    }

    // Admin methods for items
    public function createItem()
    {
        $categories = FaqCategory::orderBy('name')->get();
        return view('faq.create-item', compact('categories'));
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        FaqItem::create([
            'faq_category_id' => $request->faq_category_id,
            'question' => $request->question,
            'answer' => $request->answer,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('faq.index')->with('success', 'FAQ item created successfully.');
    }

    public function editItem(FaqItem $faqItem)
    {
        $categories = FaqCategory::orderBy('name')->get();
        return view('faq.edit-item', compact('faqItem', 'categories'));
    }

    public function updateItem(Request $request, FaqItem $faqItem)
    {
        $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $faqItem->update([
            'faq_category_id' => $request->faq_category_id,
            'question' => $request->question,
            'answer' => $request->answer,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('faq.index')->with('success', 'FAQ item updated successfully.');
    }

    public function destroyItem(FaqItem $faqItem)
    {
        $faqItem->delete();
        return redirect()->route('faq.index')->with('success', 'FAQ item deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoryExport;
use Illuminate\Http\Request;
use App\Models\Category;
use DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $sorter     = $request->sort ? explode('.', $request->sort)[0] : 'id';
        $direction  = $request->sort ? explode('.', $request->sort)[1] : 'asc';
        $categories =
        DB::table('categories')->select([
            'categories.id',
            'categories.en_name',
            'categories.ar_name',
            'categories.active',
            'categories.image',
            DB::raw("COALESCE(COUNT(DISTINCT subCats.id), 0) AS sub_count"),
            DB::raw("COALESCE(COUNT(orders.id), 0) AS orders_count"),
            DB::raw("COALESCE(SUM(orders.order_total), 0) AS services_sales"),
            DB::raw("COALESCE(SUM(orders.item_total), 0) AS items_sales"),
            DB::raw("COALESCE(SUM(orders.total_amount), 0) AS total_sales"),
            DB::raw("COALESCE(COUNT(order_rates.id), 0) AS rate_count"),
            DB::raw("COALESCE(AVG(order_rates.average), 0) AS rate_average"),
            'categories.created_at',
            'categories.updated_at',
        ])
        ->whereNull('categories.parent_id')
        ->whereNull('categories.deleted_at')
        ->leftJoin('categories as subCats', 'subCats.parent_id', '=', 'categories.id')
        ->leftJoin('orders', 'orders.cat_id', '=', 'subCats.id')
        ->leftJoin('order_rates', 'orders.id', '=', 'order_rates.order_id')
        ->orderBy($sorter, $direction)
        ->groupBy('categories.id')
        ->get();

        $categories = $categories->map(function ($cat) use ($sorter, $direction){
            $cat->sub_categories =
            DB::table('categories')->select([
                'categories.id',
                'categories.en_name',
                'categories.ar_name',
                'categories.image',
                DB::raw("COALESCE(COUNT(orders.id), 0) AS orders_count"),
                DB::raw("(SELECT COUNT(*) FROM categories AS subs WHERE subs.parent_id = categories.id ) AS sub_count"),
                DB::raw("COALESCE(SUM(orders.order_total), 0) AS services_sales"),
                DB::raw("COALESCE(SUM(orders.item_total), 0) AS items_sales"),
                DB::raw("COALESCE(SUM(orders.total_amount), 0) AS total_sales"),
                DB::raw("COALESCE(COUNT(order_rates.id), 0) AS rate_count"),
                DB::raw("COALESCE(AVG(order_rates.average), 0) AS rate_average"),
                'categories.created_at',
                'categories.updated_at',
            ])
            ->where('categories.parent_id' ,$cat->id)
            ->whereNull('categories.deleted_at')
            ->leftJoin('orders', 'orders.cat_id', '=', 'categories.id')
            ->leftJoin('order_rates', 'orders.id', '=', 'order_rates.order_id')
            ->orderBy($sorter, $direction)
            ->groupBy('categories.id')
            ->get();
            return $cat;
        });
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::where('parent_id', NULL)->get();
        return view('admin.categories.form', compact('categories'));
    }

    public function edit($id)
    {
        $category = Category::find($id);
        $categories = Category::where('parent_id', NULL)->get();
        return view('admin.categories.form', compact('category','categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'ar_name'   => 'required',
            'en_name'   => 'required',
            'image'     => 'sometimes',
            'parent_id' => 'sometimes|exists:categories,id',
            'type'      => 'required|in:1,2,3',
        ];

        $messages = [
            'ar_name.required' => 'Please enter an arabic name.',
            'en_name.required' => 'Please enter an english name.',
            'parent_id.exists' => 'Please choose a parent category.',
        ];

        $this->validate($request, $rules, $messages);

        $category = new Category;
        $category->type      = $request->type;
        $category->parent_id = $request->parent_id;
        $category->en_name   = $request->en_name;
        $category->ar_name   = $request->ar_name;
        $category->active    = $request->active ?? 1;
        if($request->hasFile('image')) {
            $name = unique_file($request->image->getClientOriginalName());
            $request->image->move(base_path().'/public/categories/',$name);
            $category->image = $name;
        }
        $category->save();

        return redirect()->route('admin.categories.index');
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'ar_name'   => 'required',
            'en_name'   => 'required',
            'image'     => 'sometimes',
            'parent_id' => 'sometimes|exists:categories,id',
            'type'      => 'required|in:1,2,3',
        ];

        $messages = [
            'ar_name.required' => 'Please enter an arabic name.',
            'en_name.required' => 'Please enter an english name.',
            'parent_id.exists' => 'Please choose a parent category.',
        ];

        $this->validate($request, $rules, $messages);

        $category = Category::findOrFail($id);
        $category->type      = $request->type;
        $category->parent_id = $request->parent_id ?? $category->parent_id;
        $category->en_name   = $request->en_name;
        $category->ar_name   = $request->ar_name;
        $category->active    = $request->active ?? 1;
        if($request->hasFile('image')) {
            $name = unique_file($request->image->getClientOriginalName());
            $request->image->move(base_path().'/public/categories/',$name);
            $category->image = $name;
        }
        $category->save();

        return redirect()->route('admin.categories.index');
    }

    public function destroy(Request $request)
    {
        Category::findOrFail($request->cat_id)->delete();
        return redirect()->route('admin.categories.index');
    }

    public function html($id)
    {
        $categories = Category::where('parent_id', $id)->get();
        return view('admin.categories.partial', compact('categories'));
    }

    public function exportForm()
    {
        return view('admin.categories.export');
    }

    public function export(Request $request)
    {
        return Excel::download(new CategoryExport($request->level, []), 'categories.xlsx');
    }
}

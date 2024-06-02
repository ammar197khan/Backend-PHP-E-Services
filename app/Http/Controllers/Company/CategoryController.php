<?php

namespace App\Http\Controllers\Company;

use App\Models\Category;
use App\Models\CompanySubscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $sorter     = $request->sort ? explode('.', $request->sort)[0] : 'id';
        $direction  = $request->sort ? explode('.', $request->sort)[1] : 'asc';

        $sub_categories = CompanySubscription::where('company_id', company()->company_id)->first();
        $sub_categories = $sub_categories ? unserialize($sub_categories->subs) : [];

        $subscriptions = Category::whereIn('id', $sub_categories)->pluck('parent_id');

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
                ->join('categories as subCats', 'subCats.parent_id', '=', 'categories.id')
                ->leftJoin('orders', 'orders.cat_id', '=', 'subCats.id')
                ->leftJoin('order_rates', 'orders.id', '=', 'order_rates.order_id')
                ->whereIn('categories.id', $subscriptions)
                ->orderBy($sorter, $direction)
                ->groupBy('categories.id')
                ->get();


        $categories = $categories->map(function ($cat) use ($sorter, $direction,$sub_categories){
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
                    ->whereIn('categories.id', $sub_categories)
                    ->orderBy($sorter, $direction)
                    ->groupBy('categories.id')
                    ->get();
            return $cat;
        });

        return view('company.categories.index', compact('categories'));
    }
}

<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class CategoryExport implements FromCollection, WithStrictNullComparison, WithHeadings
{

    protected $filters = [];
    protected $level = 1;

    protected $mainHeadings = [
        'ID',
        'English Name',
        'Arabic Name',
        'Sub Categories',
        'Orders Count',
        'Services Sales',
        'Items Sales',
        'Total Sales',
        'Rate Count',
        'Rate Average',
    ];

    protected $jobHeadings = [
        'ID',
        'English Name',
        'Arabic Name',
        'Sub Parent',
        'Parent'
    ];

    function __construct($level, $filters) {
        $this->level = $level;
        $this->filters = $filters;
    }

    public function collection()
    {
        if ($this->level == 1) {
            return $this->MainServices();
        }elseif ($this->level == 2) {
            return $this->SubServices();
        }elseif ($this->level == 3) {
            return $this->JobServices();
        }
        return $this->MainServices();
    }

    // QUERIES
    public function MainServices()
    {
        return
        DB::table('categories')->select([
            'categories.id',
            'categories.en_name',
            'categories.ar_name',
            'categories.active',
            DB::raw("COALESCE(COUNT(DISTINCT subCats.id), 0) AS sub_count"),
            DB::raw("COALESCE(COUNT(orders.id), 0) AS orders_count"),
            DB::raw("COALESCE(SUM(orders.order_total), 0) AS services_sales"),
            DB::raw("COALESCE(SUM(orders.item_total), 0) AS items_sales"),
            DB::raw("COALESCE(SUM(orders.total_amount), 0) AS total_sales"),
            DB::raw("COALESCE(COUNT(order_rates.id), 0) AS rate_count"),
            DB::raw("COALESCE(AVG(order_rates.average), 0) AS rate_average"),
        ])
        ->whereNull('categories.parent_id')
        ->whereNull('categories.deleted_at')
        ->join('categories as subCats', 'subCats.parent_id', '=', 'categories.id')
        ->leftJoin('orders', 'orders.cat_id', '=', 'subCats.id')
        ->leftJoin('order_rates', 'orders.id', '=', 'order_rates.order_id')
        ->groupBy('categories.id')
        ->get();
    }

    public function SubServices()
    {
        return
        DB::table('categories')->select([
            'categories.id',
            'categories.en_name',
            'categories.ar_name',
            'parent.en_name AS Parent',
            DB::raw("COALESCE(COUNT(orders.id), 0) AS orders_count"),
            DB::raw("(SELECT COUNT(*) FROM categories AS subs WHERE subs.parent_id = categories.id ) AS sub_count"),
            DB::raw("COALESCE(SUM(orders.order_total), 0) AS services_sales"),
            DB::raw("COALESCE(SUM(orders.item_total), 0) AS items_sales"),
            DB::raw("COALESCE(SUM(orders.total_amount), 0) AS total_sales"),
            DB::raw("COALESCE(COUNT(order_rates.id), 0) AS rate_count"),
            DB::raw("COALESCE(AVG(order_rates.average), 0) AS rate_average"),
        ])
        ->where('categories.type', 2)
        ->whereNull('categories.deleted_at')
        ->leftJoin('categories AS parent', 'categories.parent_id', '=', 'parent.id')
        ->leftJoin('orders', 'orders.cat_id', '=', 'categories.id')
        ->leftJoin('order_rates', 'orders.id', '=', 'order_rates.order_id')
        ->groupBy('categories.id')
        ->get();
    }

    public function JobServices()
    {
        return
        DB::table('categories')->select([
            'categories.id',
            'categories.en_name',
            'categories.ar_name',
            'subParent.en_name AS Sub Parent',
            'parent.en_name AS Parent'
        ])
        ->where('categories.type', 3)
        ->whereNull('categories.deleted_at')
        ->leftJoin('categories AS subParent', 'categories.parent_id', '=', 'subParent.id')
        ->leftJoin('categories AS parent', 'subParent.parent_id', '=', 'parent.id')
        ->get();
    }

    // HEADINGS
    public function headings(): array
    {
        if ($this->level == 1) {
            return $this->mainHeadings;
        }elseif ($this->level == 2) {
            return $this->mainHeadings;
        }elseif ($this->level == 3) {
            return $this->jobHeadings;
        }
        return $this->mainHeadings;
    }
}

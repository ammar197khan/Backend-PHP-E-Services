<?php

namespace App\Http\Controllers\Admin;

use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class TechnicianController extends Controller
{
    public function index(Request $request)
    {
        $sorter     = $request->sort ? explode('.', $request->sort)[0] : 'id';
        $direction  = $request->sort ? explode('.', $request->sort)[1] : 'asc';
        $technicians =
        DB::table('technicians')->select([
          'technicians.id',
          'technicians.en_name',
          'technicians.ar_name',
          'technicians.email',
          'technicians.phone',
          'technicians.type',
          'technicians.badge_id',
          'technicians.active',
          'technicians.busy',
          'technicians.online',
          'technicians.cat_ids',
          'technicians.provider_id as tech_provider_id',
          'providers.en_name AS provider',
          'rotations.en_name AS rotation',
          DB::raw("COALESCE(COUNT(DISTINCT orders.id), 0) AS orders_count"),
          DB::raw("COALESCE(SUM(orders.order_total), 0) AS services_sales"),
          DB::raw("COALESCE(SUM(orders.item_total), 0) AS items_sales"),
          DB::raw("COALESCE(SUM(orders.total_amount), 0) AS total_sales"),
          DB::raw("COALESCE(COUNT(DISTINCT order_rates.id), 0) AS rate_count"),
          DB::raw("COALESCE(AVG(order_rates.average), 0) AS rate_average"),
          'technicians.created_at',
          'technicians.updated_at',
        ])
        ->leftJoin('providers', 'technicians.provider_id', '=', 'providers.id')
        ->leftJoin('rotations', 'technicians.rotation_id', '=', 'rotations.id')
        ->leftJoin('orders', 'technicians.id', '=', 'orders.tech_id')
        ->leftJoin('order_rates', 'orders.id', '=', 'order_rates.order_id')
        ->orderBy($sorter, $direction)
        ->groupBy('technicians.id');

        if($request->has('active')){
          $technicians->where('technicians.active', $request->active);
        }

        if($request->has('provider_id')){
          $technicians->where('technicians.provider_id', $request->provider_id);
        }

        $technicians = $technicians->paginate(50);

        return view('admin.technicians.index', compact('technicians'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $technician = Technician::find($id);
        return view('admin.technicians.show', compact('technician'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}

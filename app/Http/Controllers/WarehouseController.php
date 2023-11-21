<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\WarehouseDetail;

class WarehouseController extends Controller
{
    public function createWarehouse(Request $request)
    {
        try {
            // Tạo một đối tượng Warehouse từ dữ liệu yêu cầu
            $warehouse = Warehouse::create([
                'date' => $request->input('date'),
                'total_price' => $request->input('total_price'),
                'employee_id' => $request->input('employee_id'),
                'supplier_id' => $request->input('supplier_id'),
            ]);

            // Lưu thông tin chi tiết kho vào WarehouseDetail
            $warehouseDetails = $request->input('warehouse_details'); // Đây là một mảng chứa thông tin các sản phẩm trong kho

            foreach ($warehouseDetails as $detail) {
                WarehouseDetail::create([
                    'warehouse_id' => $warehouse->warehouse_id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'unit' => $detail['unit'],
                ]);
            }

            return response()->json(['success' => true, 'data' => ['warehouse' => $warehouse, 'warehouseDetails' => $warehouseDetails]], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getWarehouse($id)
    {
        try {
            // Lấy thông tin kho cùng với chi tiết kho liên quan từ cơ sở dữ liệu
            $warehouse = Warehouse::with('warehouseDetails.product')->findOrFail($id);
            return response()->json(['success' => true, 'data' => $warehouse], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
    public function getAllWarehouse()
    {
        try {
            // Lấy tất cả các kho cùng với thông tin liên quan
            $warehouses = Warehouse::with('employee', 'supplier', 'warehouseDetails.product')->get();

            return response()->json(['success' => true, 'data' => $warehouses], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateWarehouse(Request $request, $id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);

            $warehouse->update([
                'date' => $request->input('date'),
                'total_price' => $request->input('total_price'),
                'employee_id' => $request->input('employee_id'),
                'supplier_id' => $request->input('supplier_id'),
            ]);

            // Cập nhật chi tiết kho nếu có trong yêu cầu
            if ($request->has('warehouse_details')) {
                $warehouseDetails = $request->input('warehouse_details');

                foreach ($warehouseDetails as $detail) {
                    $warehouseDetail = WarehouseDetail::where('warehouse_id', $warehouse->warehouse_id)
                        ->where('product_id', $detail['product_id'])
                        ->first();

                    if ($warehouseDetail) {
                        $warehouseDetail->update([
                            'quantity' => $detail['quantity'],
                            'unit' => $detail['unit'],
                        ]);
                    } else {
                        WarehouseDetail::create([
                            'warehouse_id' => $warehouse->warehouse_id,
                            'product_id' => $detail['product_id'],
                            'quantity' => $detail['quantity'],
                            'unit' => $detail['unit'],
                        ]);
                    }
                }
            }

            return response()->json(['success' => true, 'data' => $warehouse, 'message' => 'Warehouse updated successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Warehouse not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteWarehouse($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            $warehouse->warehouseDetails()->delete();
            $warehouse->delete();

            return response()->json(['success' => true, 'message' => 'Warehouse deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Warehouse not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function createEmployee(Request $request)
    {
        try {
            $existingAccount = Account::where('email', $request->input('email'))->first();

            if ($existingAccount) {
                return response()->json(['success' => false, 'error' => 'User with this email already exists'], 409);
            }
            $hashedPassword = bcrypt($request->input('password'));

            $account = Account::create([
                'email' => $request->input('email'),
                'password' => $hashedPassword,
                'role' => 'employee',
                'avatar' => $request->input('avatar'),
                'created_at' => now(),
            ]);

            $employee = Employee::create([
                'account_id' => $account->account_id,
                'name' => $request->input('name'),
                'phone_number' => $request->input('phone_number'),
                'gender' => $request->input('gender'),
                'birthday' => $request->input('birthday'),
                'address' => $request->input('address'),
            ]);

            return response()->json(['success' => true, 'data' => $employee], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getEmployee($id)
    {
        try {
            $employee = Employee::with('account')->findOrFail($id);
            return response()->json(
                [
                    'success' => true,
                    'data' => $employee
                ],
                200
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAllEmployees()
    {
        try {
            $employees = Employee::with('account')->get();
            return response()->json(
                [
                    'success' => true,
                    'data' => $employees
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function deleteEmployee($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function updateEmployee(Request $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            if ($request->filled(['email', 'password', 'avatar'])) {
                $accountFields = $request->only(['email', 'password', 'avatar']);
                $hashedPassword = bcrypt($accountFields['password']);
                $accountFields['password'] = $hashedPassword;
                $employee->account->update($accountFields);
            }

            if ($request->filled(['name', 'phone_number', 'gender', 'birthday', 'address'])) {
                $employeeFields = $request->only(['name', 'phone_number', 'gender', 'birthday', 'address']);
                $employee->update($employeeFields);
            }

            return response()->json([
                'success' => true,
                'data' => $employee,
                'message' => 'Employee updated successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
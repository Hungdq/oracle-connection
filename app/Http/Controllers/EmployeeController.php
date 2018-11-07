<?php

namespace App\Http\Controllers;

use App\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Log::info('::GET ALL EMPLOYEES::');
        $employees = Employee::all();
        return json_encode($employees);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info('::SAVE::');
        $employee = new Employee;
        $employee->firstName = $request->firstName;
        $employee->lastName = $request->lastName;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->birthDate = $request->birthDate;
        $employee->title = $request->title;
        $employee->department = $request->dept;
        if( $employee->save() ){
            return response('{"status":"Employee created successfully!"}',201);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Log::info('::UPDATE::');
        $employee = Employee::find($id);
        $employee->firstName = $request->firstName;
        $employee->lastName = $request->lastName;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->birthDate = $request->birthDate;
        $employee->title = $request->title;
        $employee->department = $request->dept;
        if( $employee->save() ){
            return response('{"status":"Employee updated successfully!"}',200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Log::info('::DELETE::');
        if (Employee::destroy($id)){
            return response('{"status":"Employee deleted successfully!"}',200);
        }
    }

    public function search($criteria, $value){
        Log::info('::SEARCH::');
        if ($criteria=='department'){
            $filter = 'DEPARTMENT';
        }else if ($criteria=='lastname'){
            $filter = 'LASTNAME';
        }else if ($criteria=='title'){
            $filter = 'TITLE';
        }else{
            throw new Exception('Filter criteria not valid');
        }
        $employee = Employee::where($filter, '=', $value)->get();
        return json_encode($employee);
    }
}

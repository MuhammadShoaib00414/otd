<?php

namespace App\Http\Controllers;

use App\User;
use App\Group;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadUsers(Request $request)
    {

        $import = new UsersImport();
        $filePath   = public_path('csv/new_member_to_add.xlsx');

        $import->sheets('OTD New Member adds', 'USE THIS TAB');
        Excel::import($import,  $filePath);

        //  $request  = storage_path('app/public/csv/new_member_to_add.xlsx');
        // Excel::import(new UsersImport, $request);

        // return redirect()->route('users.index')->with('success', 'User Imported Successfully');
    }

    public function deleteUsers(Request $request)
    {
        $filePath   = public_path('csv/new_member_to_add.xlsx');

        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filePath);

        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
      
        foreach ($data as $key => $row) {
            if ($key == 0) {
                continue;
            } else {
             
                // Check if there's a match in the database table
                $match = DB::table('users')->where('email', $row[0])->first();
            
                if ($match == null) {
                    return false;
                } else {
                    echo "<pre>";
                    print_r($match->id);
                    DB::table('group_user')->where('user_id', $match->id)->delete();
                    DB::table('question_user')->where('user_id', $match->id)->delete();
                    DB::table('users')->where('email', $row[0])->delete();
                    // If there's a match, delete the data
                  
                }
            }
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

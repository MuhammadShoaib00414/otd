<?php



namespace App\Imports;

use DB;
use App\User;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class SecondSheetImport implements ToArray, WithCalculatedFormulas
{
    public function array(array $rows)
    {
       
        try {
            if (count($rows) > 0) {
                foreach ($rows as $key => $row) {
                    if($key == 0){
                        continue;
                    }else{
                    if($row[1] != '' && $row[0] != '' && $row[2] != '' && $row[3] != '' && $row[4] != '' && $row[5] != '')
                        {
                           
                            $users = [
                                'name'       =>      $row[1],
                                'email'      =>      $row[0],
                                'password'   =>      Hash::make('123456789'),
                                'location'   =>      $row[2],
                                'company'    =>      $row[3],
                                'job_title'  =>      $row[4],
                                'superpower' =>      $row[5],
                                'summary'    =>      $row[6],
                                'created_at' =>      Carbon::now(),
                                'updated_at' =>      Carbon::now(),
                                'is_onboarded'=>     0  
                            ];
                           
                          
                            if (User::where('email', '=', $row[0])->count() <= 0) {
                               
                                $user = User::create($users);
    
                                $group = [
                                    'user_id' => $user->id,
                                    'group_id' => 51,
                                    'is_admin' => 0,
                                ];
                                DB::table('group_user')->insert($group);
                                
                                $question = [
                                    ['question_id'=> 1, 'user_id'=> $user->id,'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),'answer' => $row[9]],
                                    ['question_id'=> 2, 'user_id'=> $user->id,'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),'answer' => $row[8]],
                                    ['question_id'=> 3, 'user_id'=> $user->id,'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),'answer' => $row[7]],
                                    //...
                                ];
                                DB::table('question_user')->insert($question);
                              
                             }
                            
                            
                        }
                    }
                    
                }
                 

            } else {
                abort(404, 'File not found.');
            }
          } catch (Exception $e) {
          
              return $e->getMessage();
          
          } 
        } 
       

    function createUrlSlug($urlString)
    {
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $urlString);
        return $slug;
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Validator;

use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    public function index()
    {
        $image = Image::orderByDesc('id')->first();
        // dd($image->toArray());
        return view('welcome', compact('image'));
    }
    public function upload(Request $request)
    {
        $input = $request->all();
        $rules = ['imageUpload' => 'required'];
        $messages = [];
        $validator = Validator::make($request->all() , $rules, $messages);
        if ($validator->fails())
        {
            $arr = array( "status" => 400, "msg" => $validator->errors() ->first(), "result" => array());
        }
        else
        {
            try
            {
                if ($input['base64image'] || $input['base64image'] != '0') {
                    
                    $folderPath = public_path('images/');
                    $image_parts = explode(";base64,", $input['base64image']);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    // $file = $folderPath . uniqid() . '.png';
                    $filename = time() . '.'. $image_type;
                    $file =$folderPath.$filename;
                    file_put_contents($file, $image_base64);

                    $Image = new Image;
                    $Image->image = $filename;
                    $Image->save();
                }
                $msg = 'Image upload successfully.';
                \Session::flash('message', $msg );
            }
            catch(\Illuminate\Database\QueryException $ex)
            {
                $msg = $ex->getMessage();
                if (isset($ex->errorInfo[2]))
                {
                    $msg = $ex->errorInfo[2];
                }
                \Session::flash('error', $msg);
                
            }
            catch(Exception $ex)
            {
                $msg = $ex->getMessage();
                if (isset($ex->errorInfo[2]))
                {
                    $msg = $ex->errorInfo[2];
                }
                \Session::flash('error', $msg);
            }
        }
        // $this->index();
        return redirect('/');
    }
}

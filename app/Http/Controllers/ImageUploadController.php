<?php

namespace App\Http\Controllers;

use App\Models\ImageUpload;
use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $this->validate($request,[
            'latitude'=>['required'],
            'longitude'=>['required'],
            'address'=>['required','string'],
            'date'=>['required','date']
        ]);
        $user = $request->user();
        $all_photos = array();
        for($i=0;$i<4;$i++)
        {
            $photo = $request->file('image'+$i);
            $image_name = pathinfo($photo->getClientOriginalName())['filename'];
            $image_name = preg_replace('/[^A-Za-z0-9 ]/', "a", $image_name).date("Y-m-d").rand(1,100000).".".$photo->getClientOriginalExtension();
            //$image_name = preg_replace('/\s+/','_',$image_name).".".$photo->getClientOriginalExtension();
            $photo->move(public_path('uploads'),$image_name);
            array_push($all_photos,$image_name);
        }

        //$path = $photo->store('uploads/products/photos');
        $upload = new ImageUpload();
        $upload->images = implode(",",$all_photos);
        $upload->user_id = $user->id;
        $upload->address = $request->post('address');
        $upload->latitude = $request->post('latitude');
        $upload->longitude = $request->post('longitude');
        $upload->date = $request->post('date');
        $upload->save();
        return response()->json(array('data'=>$upload,'message'=>'Upload Successful'),200);
    }

    public function getImages(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $user = $request->user();
        $images = ImageUpload::where('user_id',$user->id)->distinct('date')->orderBy('date', 'desc')->get();
        return response()->json(array('message'=>'Fetched Data','data'=>$images),200);
    }

    public function getImagesByDate(Request $request)
    {
        $date = $request->date;
        date_default_timezone_set('Asia/Kolkata');
        $user = $request->user();
        if(!isset($date))
            $date = date('Y-m-d');
        $image= ImageUpload::where('date',$date)->where('user_id',$user->id)->first();
        return response()->json(array('message'=>'Fetched Data','data'=>$image),200);
    }



}

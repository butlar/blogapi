<?php

namespace App\Http\Controllers;



use Throwable;
use App\Models\Blog;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class BlogController extends Controller
{

    public function failed($message)
    {
        return [
            'status_code' => 0,
            'status_text' => 'failed',
            'message' => $message,
        ];
    }

    public function success($message, $data = null)
    {
        $res = [
            'status_code' => 1,
            'status_text' => 'success',
            'message' => $message,
        ];

        if ($data != null || $data == []) {
            $res['data'] = $data;
        }
        return $res;
    }

    public function img_upload($image)
    {
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(base_path('/public/images/'), $filename);
            $image  = '/images/'.$filename;
            return $image;
    }

    public function get()
    {
        $data = Blog::latest()->get();
        return $this->success('Blog List Fetched Successfully', $data);
    }

    public function get_single( $id)
    {
        $data = Blog::where('id',$id)->get();

        if(!Blog::find($id)){
            return $this->failed('Invalid Data');
        }

        return $this->success('Blog List Fetched Successfully', $data);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' =>  'required|string|unique:Blogs,title',
            'description' => 'required|max:150',
            'content' => 'required|string',
            'tags' => 'array',
            'read_time' => 'integer|digits_between:1,100',
            'name' => 'required|string',
            'file_image'=> 'required|mimes:jpg,jpeg,png|image'
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }
        $request['image'] = $this->img_upload($request->file('file_image'));
        $request['tags'] = trim(implode(", " , $request->tags ));
        $request['name'] = Str::ucfirst($request->name);
        $request['user_id'] = Auth::id();
        $data = Blog::create($request->all());

        return $data ? $this->success('Blog Created Successfully', $data) : $this->failed('Unable to create at this moment');
    }


    public function update(Request $request, $id)
    {
        $blog_id = Blog::find($id);

        if (!$blog_id) {
            return $this->failed('Invalid Data');
        }

        $validator = Validator::make( $request->all() , [
            'title' => 'required|string',
            'description' => 'required|max:150',
            'content' => 'required|string',
            'read_time' => 'integer|digits_between:1,100',
            'tags' => 'required|array',
            'file_image'=> 'mimes:jpg,jpeg,png|image',
            'name' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }

        if ($request->hasFile('file_image')) {
            $request['image'] = $this->img_upload($request->file('file_image'));
        }

        $request['tags'] = trim(implode(", " , $request->tags ));
        $request['name'] = Str::ucfirst($request->name);

        return $blog_id->fill($request->all())->save() ? $this->success('Records Updated Successfully') : $this->failed('Unable to Update at this moment');
    }

    public function delete($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return $this->failed('Invalid Data');
        }

        return $blog->delete() ? $this->success('Blog Deleted Successfully') : $this->failed('Unable to delete at this moment');
    }

    public function update_status(Request $request, $id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return $this->failed('Invalid Data');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }
        return $blog->update(['status' => $request->status]) ? $this->success('Status Changed Successfully') : $this->failed('Unable to Update at this moment');
    }
}

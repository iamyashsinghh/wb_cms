<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    public function list()
    {
        return view('blog.blog.list');
    }

    public function ajax_list()
    {
        $data = Blog::select('blogs.id', 'blogs.heading', 'authors.name as author_name', 'blogs.status', 'blogs.updated_at', 'blogs.created_at')
            ->join('authors', 'blogs.author_id', '=', 'authors.id')
            ->get();

        return datatables($data)->make(false);
    }

    public function manage($blog_id)
    {
        $authors = Author::all();
        if ($blog_id > 0) {
            $page_heading = 'Edit Blog';
            $data = Blog::find($blog_id);
        } else {
            $page_heading = 'Add Blog';
            $data = json_decode(json_encode([
                'id' => 0,
                'slug' => '',
                'meta_title' => '',
                'meta_description' => '',
                'meta_keywords' => '',
                'heading' => '',
                'excerpt' => '',
                'image' => '',
                'image_alt' => '',
                'summary' => '',
                'og_title' => '',
                'og_description' => '',
                'header_text' => '',
                'footer_text' => '',
                'category' => '',
                'tag' => '',
                'author_id' => '',
            ]));
        }

        return view('blog.blog.manage', compact('data', 'page_heading', 'authors'));
    }

    public function manage_process(Request $request, $blog_id = 0)
    {
        $rules = [
            'slug' => 'required|string',
            'meta_title' => 'required|string',
            'meta_description' => 'required|string',
            'meta_keywords' => 'required|string',
            'heading' => 'required|string',
            'image_alt' => 'required|string',
            'summary' => 'required|string',
        ];

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate)
                ->withInput();
        }

        try {
            $blog = Blog::find($blog_id) ?? new Blog();
            $blog->slug = $request->slug;
            $blog->meta_title = $request->meta_title;
            $blog->meta_description = $request->meta_description;
            $blog->meta_keywords = $request->meta_keywords;
            $blog->heading = $request->heading;
            $blog->excerpt = $request->excerpt;

            // Handle the image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $path = $image->storeAs('public/images/flora', $imageName);
                $blog->image = Storage::url($path);
            }

            $blog->image_alt = $request->image_alt;
            $blog->summary = $request->summary;
            $blog->og_title = $request->og_title;
            $blog->og_description = $request->og_description;
            $blog->header_text = $request->header_text;
            $blog->footer_text = $request->footer_text;
            $blog->category = $request->category;
            $blog->tag = $request->tag;
            $blog->author_id = $request->author_id;
            $blog->save();

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Blog updated successfully.']);
        } catch (\Exception $e) {
            session()->flash('status', ['success' => false, 'alert_type' => 'danger', 'message' => $e->getMessage()]);
        }

        return redirect()->back();
    }

    public function delete($blog_id)
    {
        try {
            Blog::find($blog_id)->delete();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Blog Deleted Successfully.']);
        } catch (\Exception $e) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function update_blog_status($blog_id, $status) {
        try {
            $blog = Blog::find($blog_id);
            $blog->status = $status;
            $blog->save();
            $res = response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Blog Status Updated!']);
        } catch (\Throwable $th) {
            $res = response()->json(['success' => false, 'alert_type' => 'danger', 'message' => 'Something went wrong!']);
        }
        return $res;
    }
}
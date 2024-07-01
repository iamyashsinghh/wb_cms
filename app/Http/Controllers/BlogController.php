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
        $data = Blog::select('blogs.id', 'blogs.heading', 'authors.name as author_name', 'blogs.status', 'blogs.updated_at', 'blogs.created_at', 'blogs.popular')
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
            'slug' => 'required|string|unique:blogs,slug',
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

        $content_to_trim = '<p data-f-id="pbf" style="text-align: center; font-size: 14px; margin-top: 30px; opacity: 0.65; font-family: sans-serif;">Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a></p>';

        try {
            $blog = Blog::find($blog_id) ?? new Blog();
            $blog->slug = $request->slug;
            $blog->meta_title = $request->meta_title;
            $blog->meta_description = $request->meta_description;
            $blog->meta_keywords = $request->meta_keywords;
            $blog->heading = $request->heading;
            $blog->excerpt = $request->excerpt;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('public/images/flora', $imageName);
                $blog->image = Storage::url($path);
            }

            if ($blog_id == 0) {
                $blog->publish_date = today();
                $blog->popular = 0;
                $blog->status = 0;
            }

            $blog->image_alt = $request->image_alt;
            $blog->summary = str_replace($content_to_trim, '', $request->summary);
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
        return redirect()->route('blog.list');
    }

    public function checkSlug($slug)
    {
        $count = Blog::where('slug', $slug)->count();
        return response()->json(['unique' => $count]);
    }

    public function destroy($blog_id)
    {
        try {
            Blog::find($blog_id)->delete();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Blog Deleted Successfully.']);
        } catch (\Exception $e) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function update_blog_status($blog_id, $status)
    {
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

    public function update_popular_status($blog_id, $popular_status)
    {
        try {
            $blog = Blog::find($blog_id);
            $blog->popular = $popular_status;
            $blog->save();
            $res = response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Blog Popular Updated!']);
        } catch (\Throwable $th) {
            $res = response()->json(['success' => false, 'alert_type' => 'danger', 'message' => 'Something went wrong!']);
        }
        return $res;
    }
}

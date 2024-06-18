<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::all();
        return view('blog.author.list', compact('authors'));
    }

    public function create()
    {
        return view('blog.author.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bio' => 'nullable|string'
        ]);

        $author = new Author();
        $author->name = $request->name;
        $author->bio = $request->bio;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $path = $image->storeAs('public/author_images', $imageName);
            $author->image = Storage::url($path);
        }

        $author->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Author added successfully.']);
        return redirect()->route('author.list');
    }

    public function edit($id)
    {
        $author = Author::find($id);
        return view('blog.author.edit', compact('author'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bio' => 'nullable|string'
        ]);

        $author = Author::find($id);
        $author->name = $request->name;
        $author->bio = $request->bio;

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($author->image) {
                $oldImage = str_replace('/storage', 'public', $author->image);
                Storage::delete($oldImage);
            }

            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $path = $image->storeAs('public/author_images', $imageName);
            $author->image = Storage::url($path);
        }

        $author->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Author updated successfully.']);

        return redirect()->route('author.list');
    }

    public function delete($id)
    {
        $author = Author::find($id);
        // Delete image if exists
        if ($author->image) {
            $oldImage = str_replace('/storage', 'public', $author->image);
            Storage::delete($oldImage);
        }
        $author->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Author Deleted.']);
        return redirect()->route('author.list');
    }
}

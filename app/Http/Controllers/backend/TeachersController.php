<?php

namespace App\Http\Controllers\backend;

use App\Model\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Image;

class TeachersController extends Controller
{

    public function index()
    {
        $teachers = Teacher::all();
        return view('backend.teachers.manage_teachers', ['teachers'=>$teachers]);

    }


    public function create()
    {
        return view('backend.teachers.add_teachers');
    }



    protected  function teacherValidate($request) {
        $this->validate($request, [
            'first_name' => 'required',

        ]);
    }

    protected  function imageUploade($request) {
        $teacherImage = $request->file('teacher_photo');
        $fileType = $teacherImage->getClientOriginalExtension();
        $imageName = $request->first_name.'.'.$fileType;
        $directory = 'images/teachers/';
        $imageUrl = $directory.$imageName;
         Image::make($teacherImage)->resize(300,300)->save($imageUrl);

        //$teacherImage->move($directory, $imageName);
        return $imageUrl;
    }

    protected  function teacherBasicInfoSave($request, $imageUrl ) {
        $teachers = new Teacher();
        $teachers->first_name = $request->input('first_name');
        $teachers->second_name = $request->input('second_name');
        $teachers->designation = $request->input('designation');
        $teachers->contact_number = $request->input('contact_number');
        $teachers->email = $request->input('email');
        $teachers->father_name = $request->input('father_name');
        $teachers->mother_name = $request->input('mother_name');
        $teachers->address = $request->input('address');
        $teachers->teacher_photo = $imageUrl;
        $teachers->gender = $request->input('gender');
        $teachers->status = $request->input('status');
        $teachers->save();

    }

    public function store(Request $request )
    {
        $this->teacherValidate($request);
       $imageUrl = $this->imageUploade($request);
       $this->teacherBasicInfoSave($request, $imageUrl);
        return redirect()->route('teachers.create')->with('message', "Teacher is Created Successfully");
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $data['teacher'] = Teacher::find($id);
        return view('backend.teachers.edit_teachers', $data);
    }


    public function update(Request $request)
    {
        $teacherImage = $request->file('teacher_photo');
        if($teacherImage) {
            $teacher = Teacher::findorfail($request->id);
            unlink($teacher->teacher_photo);
            $imageName = $teacherImage->getClientOriginalName();
            $directory = 'images/teachers/';
            $imageUrl = $directory.$imageName;
            Image::make($teacherImage)->save($imageUrl);

            $teacher->first_name = $request->input('first_name');
            $teacher->second_name = $request->input('second_name');
            $teacher->designation = $request->input('designation');
            $teacher->contact_number = $request->input('contact_number');
            $teacher->email = $request->input('email');
            $teacher->father_name = $request->input('father_name');
            $teacher->mother_name = $request->input('mother_name');
            $teacher->address = $request->input('address');
            $teacher->teacher_photo = $imageUrl;
            $teacher->gender = $request->input('gender');
            $teacher->status = $request->input('status');
            $teacher->save();
            return redirect('backend.teachers.manage_teachers');
            //return redirect()->route('teachers.index')->with('message', "Teacher is Updated Successfully");
        } else {
            $teacher = Teacher::findorfail($request->id);
            $teacher->first_name = $request->input('first_name');
            $teacher->second_name = $request->input('second_name');
            $teacher->designation = $request->input('designation');
            $teacher->contact_number = $request->input('contact_number');
            $teacher->email = $request->input('email');
            $teacher->father_name = $request->input('father_name');
            $teacher->mother_name = $request->input('mother_name');
            $teacher->address = $request->input('address');
            $teacher->gender = $request->input('gender');
            $teacher->status = $request->input('status');
            $teacher->save();
            return redirect('backend.teachers.manage_teachers');
        }

    }


    public function destroy($id)
    {
        $teacher = Teacher::find($id);
        $teacher->delete();
        return redirect()->route('teachers.create')->with('message',"Teachers is deleted successfully");
    }

    public function changeStatus(Request $request)
    {
        $teacher =  Teacher::find($request->id);
        $teacher->status = !$teacher->status;
        $teacher->save();
        return redirect()->route('teachers.create');
    }
}

<?php
namespace App\Http\Controllers;
use App\Models\{Course,CourseModule};
use Illuminate\Http\Request;
class ModuleController extends Controller {
    public function store(Request $request, Course $course) {
        $this->authorize('update',$course);
        $v=$request->validate(['title'=>'required|string|max:255','description'=>'nullable|string']);
        $course->modules()->create(['title'=>$v['title'],'description'=>$v['description']??null,'order'=>$course->modules()->max('order')+1]);
        return back()->with('success','✅ Módulo agregado.');
    }
    public function destroy(Course $course, CourseModule $module) {
        $this->authorize('update',$course); $module->delete(); return back()->with('success','🗑️ Módulo eliminado.');
    }
}

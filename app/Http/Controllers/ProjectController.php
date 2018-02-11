<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{

    private function getFolders($project_path) {
      $folders = array();  
      $folder_names = scandir($project_path);
      foreach ($folder_names as $folder_name) {
        if($folder_name == '.' || $folder_name == '..')
          continue;
        $folder_path = $project_path . '/' . $folder_name;
        if(!is_dir($folder_path))
          continue;
        $folders[] = array(
          'name' => $folder_name,
          'files' => array_diff(scandir($folder_path), array('..', '.'))
        );
      }
      return $folders;
    }

    public function showFiles($slug) {
      $project_path = public_path() . "/repo/" . $slug;
      return view('50_project.51_tabs.blueprints', array(
        'project_url' => url("/project") . '/' . $slug,
        'repo_url' => url("/repo") . '/' . $slug, 
        'folders' => $this->getFolders($project_path)
      ));
    }

    public function show($slug = NULL)
    {
      $parsedown = new \Parsedown();

      $project = DB::table('projects')->where('slug', $slug)->first();;

      $project_path = public_path() . "/repo/" . $project->slug;
      $images_path = $project_path . '/images';

      $image_urls = array();
      $images = scandir($images_path);
      foreach ($images as $image) {
        if($image == '.' || $image == '..')
          continue;
        $image_urls[] = url("/repo/" . $project->slug . '/images') . '/' . $image; 
      }  

      return view('20_platform.project', array(
        'project' => $project, 
        'project_url' => url("/project") . '/' . $project->slug,
        'repo_url' => url("/repo") . '/' . $project->slug, 
        'details' => $parsedown->text(file_get_contents($project_path . "/details.md")),
        'details_raw' => file_get_contents($project_path . "/details.md"),
        'images' => $image_urls,
        'folders' => $this->getFolders($project_path),
        'faq' => 'xxx',
        'project_faq' => 'xxx'));
    }

    private function slugify($string, $replace = array(), $delimiter = '-') {
      // https://github.com/phalcon/incubator/blob/master/Library/Phalcon/Utils/Slug.php
      if (!extension_loaded('iconv')) {
        throw new Exception('iconv module not loaded');
      }
      // Save the old locale and set the new locale to UTF-8
      $oldLocale = setlocale(LC_ALL, '0');
      setlocale(LC_ALL, 'en_US.UTF-8');
      $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
      if (!empty($replace)) {
        $clean = str_replace((array) $replace, ' ', $clean);
      }
      $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
      $clean = strtolower($clean);
      $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
      $clean = trim($clean, $delimiter);
      // Revert back to the old locale
      setlocale(LC_ALL, $oldLocale);
      return $clean;
    }

    public function newProject(Request $request) {
      if (Auth::check()) {
        $project = new Project;
        $project->title = $request->title;
        $project->description = $request->description;
        $project->slug = $this->slugify($request->title) . '-' . uniqid();
        $project->license = 'xxx'; //$request->license;
        Auth::user()->projects()->save($project);
        $project->save();

        $project_path = public_path() . "/repo/" . $project->slug;
        @mkdir($project_path, 0700, TRUE);
        file_put_contents($project_path . "/details.md", '**test**');

        $images_path = $project_path . '/images';
        @mkdir($images_path, 0700, TRUE); 
        $images = $request->file('images');
        foreach ($images as $image) {
          if(Utils::checkFile($image))
            $image->move($images_path, $image->getClientOriginalName());
        }  

        return redirect('/project/' . $project->slug);
      }
    }

    public function update(Request $request, $slug) {
      if (!Auth::check())
        return;

      $project = Project::where('slug', $slug)->first();
      if($project->owner()->first()->id != Auth::user()->id)
        return;

      $project_path = public_path() . "/repo/" . $project->slug;
      if ($request->has('folder')) {
        $folder = $request->input('folder');
        @mkdir($project_path . '/' . $folder, 0700, TRUE);

        if ($request->has('files')) {
          $files = $request->file('files');
          foreach ($files as $file) {
            if(Utils::checkFile($file))
              $file->move($project_path . '/' . $folder, $file->getClientOriginalName());
          }
        }
      }

      if($request->has('description')) {
        $project->description = $request->input('description');
        $project->save();
      }

      if($request->has('details')) {
        $details = $request->input('details');
        file_put_contents($project_path . "/details.md", $details);        
      }

      if($request->has('redirect'))
        return redirect('/project/' . $project->slug);

      return 'done';
    }

}
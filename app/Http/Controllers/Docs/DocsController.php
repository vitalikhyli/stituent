<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use File;
use Illuminate\Http\Request;

class DocsController extends Controller
{
    public function index($section = null, $subsection = null, $page = null)
    {
        $current_path = substr(url()->current(), strpos(url()->current(), '/docs'));

        //Default section if none given
        if (! $section) {
            $section = 'office';
            $current_path .= '/'.$section;
        }

        $navigation = [];
        $is_next_topic = false;
        $next_topic = [];
        $i = 0;

        $directories = File::directories(resource_path('views/docs/'.$section));

        foreach ($directories as $dir) {
            $dir = last(explode('/', $dir));

            $files = File::allFiles(resource_path('views/docs/'.$section.'/'.$dir));

            $contents = [];

            foreach ($files as $file) {
                $path = substr((string) $file, strpos((string) $file, '/docs'), strrpos((string) $file, '/') - strpos((string) $file, '/docs'));

                $file = str_replace('.blade.php', '', $file->getFilename());
                $file_formatted = str_replace('-', ' ', $file);
                $file_formatted = preg_replace("/[^a-zA-Z\s]/", '', $file_formatted);
                $file_formatted = trim($file_formatted);

                $path = '/docs/'.$section.'/'.$dir.'/'.$file;

                $contents[$path] = $file_formatted;

                if ($current_path == '/docs/'.$section && $i == 0) {
                    $is_next_topic = true;
                    $current_page = $section.' Docs';
                }

                if ($is_next_topic) {
                    $is_next_topic = false;
                    $next_topic[$path] = $file_formatted;
                }

                if ($path == $current_path) {
                    $is_next_topic = true;
                    $current_page = $file_formatted;
                }

                $i++;
            }

            $dir = str_replace('-', ' ', $dir);
            $dir = preg_replace("/[^a-zA-Z\s]/", '', $dir);
            $navigation[$dir] = $contents;
        }

        if ($subsection) {
            if (! $page) {
                return view('docs.'.$section.'.'.$subsection.'.index', compact('navigation',
                                                                               'section',
                                                                               'current_path',
                                                                               'next_topic',
                                                                               'current_page'));
            } else {
                return view('docs.'.$section.'.'.$subsection.'.'.$page, compact('navigation',
                                                                                'section',
                                                                                'current_path',
                                                                                'next_topic',
                                                                                'current_page'));
            }
        }

        if (! $subsection) {
            return view('docs.'.$section.'.index', compact('navigation',
                                                                         'section',
                                                                         'current_path',
                                                                         'next_topic',
                                                                         'current_page'));
        }
    }
}

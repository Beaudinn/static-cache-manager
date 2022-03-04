<?php

namespace DoubleThreeDigital\StaticCacheManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Statamic\Facades\Site;
use Statamic\Support\Str;

class ClearController
{
    public function __invoke(Request $request)
    {
        $paths = explode(PHP_EOL, $request->get('paths'));

        foreach ($paths as $path) {
            $this->delete(trim($path));
        }

        return redirect()->back();
    }

    protected function delete($path): void
    {
	    $paths = config('statamic.static_caching.strategies.full.path');

	    if(!array_key_exists(Site::selected()->handle(), $paths)){
		    throw new Exception('Current site path not found');
	    }

	    $basePath = $paths[Site::selected()->handle()];
        $path = $basePath . $path; //Str::ensureLeft($path, '/');

        var_dump($path); die();
        if (File::isDirectory($path)) {
            $this->deleteDirectory($path);
        }

        $this->deleteFile($path);
    }

    protected function deleteFile($path): void
    {
        if (! Str::of($path)->contains('*')) {
            $path .= '_*';
        }

        foreach (File::glob($path) as $file) {
            File::delete($file);
        }
    }

    protected function deleteDirectory($path): void
    {
        File::deleteDirectory($path);
    }
}

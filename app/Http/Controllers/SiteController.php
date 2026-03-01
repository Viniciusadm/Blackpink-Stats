<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Services\VideosService;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function home(Request $request, VideosService $videosService)
    {
        $videos = Video::all();

        $date = $request->query('date');
        $admin = $request->query->has('admin');

        foreach ($videos as $video) {
            if ($date) {
                $views = $videosService->byDate($video, $date);
            } else {
                $views = $videosService->views($video);
            }

            $daysTo = $videosService->daysTo($video, $views, $date);

            $video->set('views', $views);
            $video->set('days_to', $daysTo['days']);
            $video->set('next', $daysTo['next']);
            $video->set('media', $daysTo['media']);
        }

        $videos = $videos->sortByDesc('views')->values();

        return view('home', [
            'videos' => $videos,
            'admin' => $admin,
            'classes' => [
                1 => 'primeiro',
                2 => 'segundo',
                3 => 'terceiro',
            ],
        ]);
    }

    public function details(string $slug)
    {
        $video = Video::where('slug', $slug)->first();

        if ($video) {
            return view('details', [
                'video' => $video,
            ]);
        }

        return response()->view('notFound', [], 404);
    }

    public function notFound()
    {
        return response()->view('notFound', [], 404);
    }
}

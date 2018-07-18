<?php

namespace App\Console;

use DB;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $filePath = 'storage/logs/schema'.date('Ymd').'.log';
        $schedule->call(function () {
            $result = DB::select("SELECT id_noticia, id_tipo_fuente FROM noticia WHERE date_format(fecha, '%Y-%m') = '2015-01'");
            Log::info('Asi se manda un log');
            Log::alert('Log de alerta');
            Log::error('Log de error');
            Log::warning('Log de warning');
            Log::debug('Log debug');
            dd($result);
        })
        ->appendOutputTo($filePath);
        // ->emailOutputTo('klonate@gmail.com');
    }
}

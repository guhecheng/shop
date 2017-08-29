<?php

namespace App\Console;

use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->call(function() {
            $orders = DB::table('orderinfo')->where([
                ['status', '<=', 1],
                ['create_time', '<=', date("Y-m-d H:i:s", time()-30*60)]
            ])->select('order_no')->get();
            file_put_contents('cron.log', date("Y-m-d H:i:s") . var_export($orders, true) . PHP_EOL, FILE_APPEND);
            if ($orders->isEmpty()) return;
            foreach ($orders as $order) {
                DB::table('orderinfo')->where('order_no', $order->order_no)->update(['status' => 6]);
                DB::table('user')->where('userid', $order->uid)->increment('score', $order->score);
                $item = DB::table('order')->where('order_no', $order->order_no)->select('skuid', 'count')->get();
                foreach ($item as $key=>$value) {
                    DB::table('goodssku')->where('sku_id', $value->skuid)->increment('num', $value->count);
                }
            }
        })->everyTenMinutes();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}

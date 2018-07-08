<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\PhoneNumber;
use App\Handlers\SmsSendsHandler;

class SmsSend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $phoneNumber;
    public function __construct(PhoneNumber $pn)
    {
        // 队列任务构造器中接收了 Eloquent 模型，将会只序列化模型的 ID
        $this->phoneNumber = $pn;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $slug = app(SmsSendsHandler::class)->sends($this->phoneNumber->phone, $this->phoneNumber->nc);
        if ($slug[0]=='1') {
            \DB::table('phone_numbers')->where('phone', $this->phoneNumber->id)->update(['isok' => $slug]);
        }else{
            \DB::table('phone_numbers')->where('phone', $this->phoneNumber->id)->update(['message' => $slug[2]]);
        }
    }
}

<?php

namespace App\Jobs;

use App\Models\Banner;
use App\Models\events;
use App\Models\OrdersModel;
use App\Models\UsersModel;
use App\Models\Voucher;
use App\Models\voucherToMain;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class EventMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
           
                DB::beginTransaction();
                DB::table('log_jobs')->insert([
                    'log' => 'EventMail ',
                ]);
                $events = events::where('event_day', date('d'))->where('event_month', date('m'))->where('status', 2)->first();
                // dd($events);
                if (!$events) {
                    check_var('Không có sự kiện nào', 404);
                    return;
                }
                check_var($events->event_title . "Đã Bắt Đầu Chạy...", 200);
                $voucherData = json_decode($events->voucher_apply);
                $images = json_decode($events->images);
                $queryUser = UsersModel::query();
                $queryUser->where('status', 1);
                if (isset($events->where_order)) {
                    $order = OrdersModel::whereIn('user_id', $queryUser->pluck('id'))
                    ->select('user_id', DB::raw('count(*) as total_orders'))
                    ->groupBy('user_id')
                    ->having('total_orders', '>', $events->where_order)
                    ->get();
                    $queryUser->whereIn('id', $order->pluck('user_id'));
                }
                if (isset($events->where_price)) {
                    $order = OrdersModel::whereIn('user_id', $queryUser->pluck('id'))
                    ->select('user_id', DB::raw('sum(total_amount) as total_amount'))
                    ->groupBy('user_id')
                    ->having('total_amount', '>', $events->where_price)
                    ->get();
                    $queryUser->whereIn('id', $order->pluck('user_id'));
                }
                if (isset($events->point)) {
                    $queryUser->where('point', '>', $events->point);
                }
                $today = date('m-d');
                $birthdayUsers = UsersModel::whereRaw("DATE_FORMAT(datebirth, '%m-%d') = ?", [$today])->pluck('id');
                $eventTitle = null;
                if ($events && $events->event_day == date('d') && $events->event_month == date('m')) {
                        $queryUser->orWhereIn('id', $birthdayUsers);
                        $users = $queryUser->get();
                        $voucherEvent = [
                                            'title' => $events->event_title ?? "Mừng ngày $today",
                                            'description' => $events->description ?? "Chúc mừng ngày $today",
                                            'image' => $images[0] ?? null,
                                            'quantity' => $voucherData->voucher_quantity,
                                            'limitValue' => $voucherData->voucher_limit,
                                            'ratio' => $voucherData->voucher_ratio,
                                            'code' => $voucherData->voucher_code,
                                            'status' => 2,
                                            'create_by' => 137,
                                            'update_by' => 137,
                                            'created_at' => Carbon::now(),
                                            'updated_at' => Carbon::now(),
                                            'min' => null,
                                            'is_event' => 1,
                                        ];
                        $voucherMain = new voucherToMain();
                        $voucherMain->fill($voucherEvent);
                        $voucherMain->save();
                        foreach ($users as $user) {
                            $param = [
                                'type' => 'main',
                                'status' => 2,
                                'create_by' => 137,
                                'update_by' => 137,
                                'code' => $voucherData->voucher_code,
                                'user_id' => $user->id,
                                'shop_id' => null,
                                'max' => $voucherData->voucher_limit,
                                'min' => null,
                                'ratio' => $voucherData->voucher_ratio,
                                'title' => $events->event_title ?? "Mừng ngày $today",
                                'description' => $events->description ?? "Chúc mừng ngày $today",
                                ];
                            $voucher = new Voucher();
                                    $voucher->fill($param);
                                    $voucher->save();
                        }
                        
                        Banner::where('status', 2)->update(['status' => 15]);
                        foreach ($images as $image) {
                            $BannerEvent = [
                                'title' => $events->event_title ?? "Mừng ngày $today",
                                'content' => $events->description ?? "Chúc mừng ngày $today",
                                'image' => $image ?? null,
                                'URL' => "Chưa Thiết KÉ",
                                'status' => 2,
                                'index' => 1,
                                'create_by' => 137,
                                'update_by' => 137,
                            ];
                            $banner = new Banner();
                            $banner->fill($BannerEvent);
                            $banner->save();
                        }
                    
                    $eventTitle = $events[$today];
                }
                if ($birthdayUsers->isNotEmpty()) {
                    foreach ($birthdayUsers as $user) {
                                $userMail = UsersModel::where('id', $user)->pluck('email');
                                $voucherHappyBirthDay = voucherToMain::where('code', 'HAPPYBIRTHDAY')->first();
                                if (!$voucherHappyBirthDay) {
                                    $voucherEvent = [
                                        'title' => "Chúc mừng sinh nhật bạn",
                                        'description' => "Chúc mừng sinh nhật bạn",
                                        'image' => "Chưa Thiết KÉ",
                                        'quantity' => 1,
                                        'limitValue' => 50000,
                                        'ratio' => 0.2,
                                        'code' => "HAPPYBIRTHDAY$user$userMail",
                                        'status' => 2,
                                        'create_by' => 137,
                                        'update_by' => 137,
                                        'created_at' => Carbon::now(),
                                        'updated_at' => Carbon::now(),
                                        'min' => null,
                                        'is_event' => null,
                                    ];
                                    $voucherHappyBirthDay = new voucherToMain();
                                    $voucherHappyBirthDay->fill($voucherEvent);
                                    $voucherHappyBirthDay->save();
                                }
                                    $param = [
                                        'type' => 'main',
                                        'status' => 2,
                                        'create_by' => 137,
                                        'update_by' => 137,
                                        'code' => $voucherData->voucher_code,
                                        'user_id' => $user,
                                        'shop_id' => null,
                                        'max' => $voucherData->voucher_limit,
                                        'min' => null,
                                        'ratio' => $voucherData->voucher_ratio,
                                        'title' => $events->event_title ?? "Mừng ngày $today",
                                        'description' => $events->description ?? "Chúc mừng ngày $today",
                                    ];
                                    $voucher = new Voucher();
                                    $voucher->fill($param);
                                    $voucher->save();
                        SendNotification::dispatch("Chúc mừng sinh nhật", "VNSHOP Chúc mừng sinh nhật bạn với món quà nho nhỏ", $user, null, "Chưa Thiết KÉ");
                        sendMailBirthDay::dispatch($userMail, "CHÚC MỪNG SINH NHẬT BẠN", $voucherHappyBirthDay->code);
                    }
                }
                if (isset($events->event_title) === null) {
                    $eventTitle = 'VNSHOP có ưu đãi hấp dẫn sắp diễn ra, hãy kiểm tra ngay!';
                }
                if (isset($events->is_mail) == 1) {
                    SendMailEvent::dispatch($users, $events->event_title ?? $eventTitle, $voucherMain->code);
                }
                SendNotiEvent::dispatch($users, $events->event_title ?? $eventTitle, $events->description ?? "Chúc mừng ngày $today", $images[0] ?? null);
                DB::commit();
                check_var($events->event_title, 200);
            
            } catch (\Throwable $th) {
                check_var($th->getMessage(), 400);
                DB::rollBack();
            }


           
    }
}

<?php

// مجموعه متدهای کار با دیتابیس
include("assets/abed.php");

// دریافت اطلاعات پیام ارسال شده
$data = json_decode( file_get_contents('php://input') );

// توقف اسکریپت در صورت خالی بودن پیام
if (empty($data)) {
    exit();
}

// استخراج چت آی دی و نام کاربری از پیام
$username = $data->message->chat->username;
$chat_id = $data->message->chat->id;

// بررسی تکراری بودن کاربر در دیتابیس
if (sqlScalar("select count(chat_id) from chats where chat_id='$chat_id'") > 0) {
    
    // بروزرسانی نام کاربری
    sqlExecute("update chats set username='$username' where chat_id='$chat_id'");
} else {
    
    // ثبت چت آی دی و نام کاربری
    sqlExecute("insert into chats values ('$chat_id','$username')");
}

// بررسی پرداخت موفق فاکتور
$payment = $data->message->successful_payment;

if (!empty($payment) && $username != 'receipt') {

    // دریافت آی دی مدیر از دیتابیس
    // یوزر مدیر را با puriya جایگزین کنید
    $chat_id = sqlScalar("select chat_id from chats where username='webarman'");
    
    // دریافت مبلغ پرداختی به تومان
    $amount = ((int) $payment->total_amount) / 10;
    
    // متن پیام
    $message = urlencode("مبلغ $amount تومان توسط $username پرداخت شد");
    
    // ارسال پیام به مدیر
    $curl = curl_init();
    //1400797669
	//1400797669:27883ff63bbb84eb921c0177bc9136af7a5fe454
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://tapi.bale.ai/bot1400797669:27883ff63bbb84eb921c0177bc9136af7a5fe454/sendMessage?chat_id=$chat_id&text=$message",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
    ));
    
    $response = curl_exec($curl);
    curl_close($curl);

}

?>
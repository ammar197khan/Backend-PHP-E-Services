@component('mail::message')
# Hello {{ $user->en_name }}

Finally, you can request your maintenance services super easily using **Qreeb** mobile app. There is a membership created for you by your employer.

**username: {{ $user->email }}**

**password: {{ $password }}**

You can find us on your mobile app store.
<br>
<center>
  <a href='https://play.google.com/store/apps/details?id=com.qreebs.ticketing.user'>
    <img alt='Get it on Google Play' style="height:70px; text-align: center;" src="{{ ('http://admin.qreebs.com/android.png') }}"/>
  </a>
  <a href='https://apps.apple.com/us/app/qreeb-%D9%82%D8%B1%D9%8A%D8%A8/id1488260389'>
    <img alt='Get it on Apple Store' style="height:70px; text-align: center;" src="{{ ('http://admin.qreebs.com/ios.png') }}"/>
  </a>
</center>

<br>
Thanks,<br>
{{ config('app.name') }}
<br>
<br>

---
<br>
<h1 style='direction:rtl; text-align: right'>مرحبا {{$user->ar_name}}</h1>

<p style='direction:rtl; text-align: right'>
  الآن يمكنك طلب خدمات الصيانة بسهولة فائقة عن طريق استخدام تطبيق قريب, فقد تم انشاء عضوية خاصة لك من قبل شركتك.
</p>

<p style='direction:rtl; text-align: right'>
  <b>اسم المستخدم: {{ $user->email }}</b>
</p>

<p style='direction:rtl; text-align: right'>
  <b>كلمة المرور: {{ $password }}</b>
</p>


<p style='direction:rtl; text-align: right'>
  متاح اللآن على متجر التطبيقات.
</p>

<center>
  <a href='https://play.google.com/store/apps/details?id=com.qreebs.ticketing.user'>
    <img alt='Get it on Google Play' style="height:70px; text-align: center;" src="{{ ('http://admin.qreebs.com/android.png') }}"/>
  </a>
  <a href='https://apps.apple.com/us/app/qreeb-%D9%82%D8%B1%D9%8A%D8%A8/id1488260389'>
    <img alt='Get it on Apple Store' style="height:70px; text-align: center;" src="{{ ('http://admin.qreebs.com/ios.png') }}"/>
  </a>
</center>

<p style='direction:rtl; text-align: right'>
  <br>
  شكراً,
  <br>
  قريب
</p>

<br>
<img src="{{ ('http://admin.qreebs.com/qreeb.png') }}">

@endcomponent

<div style="margin-left: 18.5%;;margin-top:6%;font-family: 'Poppins';" class="data">
    <h1 style="font-size: 16px;font-weight: 600;">Hello,</h1>
    <p style="font-size: 14px; line-height: 26px; font-weight: 400;">
        Thank you very much for you order. <br />
        Your Order: <span style="font-size: 16px; font-weight: 600;">
            {{ $content->id }}
        </span>Dated:  <span style="font-size: 16px; font-weight: 600;"> {{ $content->created_at }}</span>
        has been confirmed by Qareeb.
    </p>
    <div style="font-size: 14px;margin-top: 38px;">
        <h3 style="margin-top: 40px;font-weight: 600;">Regards,</h3>
        <h3 style="margin-top: 0;font-weight: 600;">Qareeb</h3>
    </div>
</div>


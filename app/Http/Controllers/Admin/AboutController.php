<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\AboutUs;
use App\Models\Complain;
use App\Models\Privacy;
use App\Models\PushNotify;
use App\Models\Term;
use App\Models\User;
use App\Models\UserNot;
use App\Models\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AboutController extends Controller
{
    public function index()
    {
        $about = AboutUs::first();

        return view('admin.settings.abouts.index', compact('about'));
    }


    public function edit()
    {
        $about = AboutUs::first();
        return view('admin.settings.abouts.single', compact('about'));
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'en_text' => 'required',
                'ar_text' => 'required',
            ],
            [
                'en_text.required' => 'English text is required',
                'ar_text.required' => 'Arabic text is required',
            ]
        );

        $about = AboutUs::first();
            $about->en_text = $request->en_text;
            $about->ar_text = $request->ar_text;
        $about->save();

        return redirect('/admin/settings/about')->with('success', 'Updated successfully');
    }

    public function terms()
    {
        $term = Term::first();

        return view('admin.settings.terms.index', compact('term'));
    }


    public function terms_edit()
    {
        $term = Term::first();
        return view('admin.settings.terms.single', compact('term'));
    }


    public function terms_update(Request $request)
    {
        $this->validate($request,
            [
                'en_text' => 'required',
                'ar_text' => 'required',
            ],
            [
                'en_text.required' => 'English text is required',
                'ar_text.required' => 'Arabic text is required',
            ]
        );

        $about = Term::first();
        $about->en_text = $request->en_text;
        $about->ar_text = $request->ar_text;
        $about->save();

        return redirect('/admin/settings/terms')->with('success', 'Updated successfully');
    }

    public function privacy()
    {
        $privacy = Privacy::first();

        return view('admin.settings.privacy.index', compact('privacy'));
    }


    public function privacy_edit()
    {
        $privacy = Privacy::first();
        return view('admin.settings.privacy.single', compact('privacy'));
    }


    public function privacy_update(Request $request)
    {
        $this->validate($request,
            [
                'en_text' => 'required',
                'ar_text' => 'required',
            ],
            [
                'en_text.required' => 'English text is required',
                'ar_text.required' => 'Arabic text is required',
            ]
        );

        $privacy = Privacy::first();
        $privacy->en_text = $request->en_text;
        $privacy->ar_text = $request->ar_text;
        $privacy->save();

        return redirect('/admin/settings/privacy')->with('success', 'Updated successfully');
    }

    public function complains()
    {
        $complains = Complain::get();

        return view('admin.settings.complains.index', compact('complains'));
    }

    public function send_complains(Request $request)
    {
        $complain = Complain::whereId($request->complain_id)->first();

        if($complain->user_id == 0)
        {
            return redirect('/admin/settings/complains')->with('error','Wrong user id');
        }

        $ar_text = 'تم إرسال الرد علي الإيميل برجاء المتابعه.';
        $en_text = 'Replay send to your mail,please check the details';

        UserNot::create
        (
            [
                'type' => 'notify',
                'user_id' => $complain->user_id,
                'ar_text' => $ar_text,
                'en_text' => $en_text
            ]
        );

        $token = UserToken::where('user_id', $complain->user_id)->pluck('token');
        PushNotify::user_send($token,$ar_text,$en_text,'notify');

        $email = User::whereId($complain->user_id)->select('email')->first()->email;
        $dataEmail = [
            'subject' => 'Replay about your problem',
            'email' => $email,
            'content' => $request->get_problem
        ];
        Mail::send('admin.emails.email',$dataEmail, function ($message) use ($dataEmail) {
            $message->from('support@qareeb.com','Support@Qareeb')
                ->to($dataEmail['email'])
                ->subject('Qareeb | Customer Support');
        });

        return redirect('/admin/settings/complains')->with('success','Send successfully');
    }


}

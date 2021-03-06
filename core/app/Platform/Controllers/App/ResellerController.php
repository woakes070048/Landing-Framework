<?php namespace Platform\Controllers\App;

use \Platform\Controllers\Core;
use Illuminate\Http\Request;
use App\Notifications\ResellerCreated;

class ResellerController extends \App\Http\Controllers\Controller {

  /*
   |--------------------------------------------------------------------------
   | Reseller Controller
   |--------------------------------------------------------------------------
   |
   | Reseller related logic
   |--------------------------------------------------------------------------
   */

  /**
   * Reseller management
   */
  public function showResellers()
  {
    return view('platform.admin.resellers.resellers');
  }

  /**
   * New reseller
   */
  public function showNewReseller()
  {
    // Get users who can be reseller
    $users = \App\User::whereNull('is_reseller_id')->get();

    if (count($users) == 0) {
      $users[''] = trans('global.no_users_for_reseller');
    }

    $header_gradient_start = '#138dfa';
    $header_gradient_end = '#138dfa';
    $header_image = '/templates/assets/images/visuals/landing-screens-en.png';
    $header_title = trans('website.header_01_line');
    $header_cta = trans('website.header_cta');

    // Payment provider, check if setting exists
    $main_reseller = Core\Reseller::get();

    $payment_provider = 'CUSTOM';
    if (isset($main_reseller->settings['payment_provider'])) {
      $payment_provider = $main_reseller->settings['payment_provider'];
    } else {
      // Setting does not exist, check .env config
      if (env('AVANGATE_KEY', '') != '' && $main_reseller->stripe_key == null) {
        $payment_provider = 'AVANGATE';
      } elseif ($main_reseller->stripe_key != null) {
        $payment_provider = 'STRIPE';
      }
    }

    $user_query_parameter_placeholder = (isset($main_reseller->settings['user_query_parameter'])) ? $main_reseller->settings['user_query_parameter'] : 'user_id';
    $affiliate_query_parameter_placeholder = (isset($main_reseller->settings['affiliate_query_parameter'])) ? $main_reseller->settings['affiliate_query_parameter'] : 'source';

    return view('platform.admin.resellers.reseller-new', compact('main_reseller', 'users', 'header_gradient_start', 'header_gradient_end', 'header_image', 'header_title', 'header_cta', 'payment_provider', 'user_query_parameter_placeholder', 'affiliate_query_parameter_placeholder'));
  }

  /**
   * Edit reseller
   */
  public function showEditReseller()
  {
    $sl = request()->input('sl', '');

    if($sl != '') {
      $qs = Core\Secure::string2array($sl);
      $reseller = \App\Reseller::where('id', $qs['reseller_id'])->first();
      $user = \App\User::where('is_reseller_id', $qs['reseller_id'])->first();

      // Get users who can be reseller
      $users = \App\User::whereNull('is_reseller_id')->orWhere('is_reseller_id', $qs['reseller_id'])->get();

      if (count($users) == 0) {
        $users[''] = trans('global.no_users_for_reseller');
      }

      $website_active = (isset($reseller->settings['website_active'])) ? $reseller->settings['website_active'] : true;
      $header_gradient_start = (isset($reseller->settings['header_gradient_start'])) ? $reseller->settings['header_gradient_start'] : '#138dfa';
      $header_gradient_end = (isset($reseller->settings['header_gradient_end'])) ? $reseller->settings['header_gradient_end'] : '#138dfa';
      $header_image = (isset($reseller->settings['header_image'])) ? $reseller->settings['header_image'] : '/templates/assets/images/visuals/landing-screens-en.png';
      $header_title = (isset($reseller->settings['header_title'])) ? $reseller->settings['header_title'] : trans('website.header_01_line');
      $header_cta = (isset($reseller->settings['header_cta'])) ? $reseller->settings['header_cta'] : trans('website.header_cta');

      // Payment provider, check if setting exists
      $payment_provider = 'CUSTOM';
      if (isset($reseller->settings['payment_provider'])) {
        $payment_provider = $reseller->settings['payment_provider'];
      } else {
        // Setting does not exist, check .env config
        if (env('AVANGATE_KEY', '') != '' && $reseller->stripe_key == null) {
          $payment_provider = 'AVANGATE';
        } elseif ($reseller->stripe_key != null) {
          $payment_provider = 'STRIPE';
        }
      }

      // Current (main) reseller
      $main_reseller = Core\Reseller::get();

      $user_query_parameter_placeholder = (isset($main_reseller->settings['user_query_parameter'])) ? $main_reseller->settings['user_query_parameter'] : 'user_id';
      $affiliate_query_parameter_placeholder = (isset($main_reseller->settings['affiliate_query_parameter'])) ? $main_reseller->settings['affiliate_query_parameter'] : 'source';

      $user_query_parameter = (isset($reseller->settings['user_query_parameter'])) ? $reseller->settings['user_query_parameter'] : '';
      $affiliate_query_parameter = (isset($reseller->settings['affiliate_query_parameter'])) ? $reseller->settings['affiliate_query_parameter'] : '';

      return view('platform.admin.resellers.reseller-edit', compact('sl', 'main_reseller', 'reseller', 'user', 'users', 'website_active', 'header_gradient_start', 'header_gradient_end', 'header_image', 'header_title', 'header_cta', 'payment_provider', 'user_query_parameter', 'affiliate_query_parameter', 'user_query_parameter_placeholder', 'affiliate_query_parameter_placeholder'));
    }
  }

  /**
   * Add new reseller
   */
  public function postNewReseller()
  {
    $input = array(
      'timezone' => request()->input('timezone'),
      'language' => request()->input('language'),
      'name' => request()->input('name'),
      'support_email' => request()->input('support_email'),
      'domain' => request()->input('domain'),
      'logo' => request()->input('logo', null),
      'logo_square' => request()->input('logo_square', null),
      'favicon' => request()->input('favicon', null),
      'website_active' => (bool) request()->input('website_active', false),
      'header_gradient_start' => request()->input('header_gradient_start', null),
      'header_gradient_end' => request()->input('header_gradient_end', null),
      'header_image' => request()->input('header_image', null),
      'header_title' => request()->input('header_title', null),
      'header_cta' => request()->input('header_cta', null),
      'user_id' => request()->input('user_id'),
      'active' => (bool) request()->input('active', false),
      'mail_driver' => request()->input('mail_driver', null),
      'mail_from_name' => request()->input('mail_from_name', null),
      'mail_from_address' => request()->input('mail_from_address', null),
      'mail_host' => request()->input('mail_host', null),
      'mail_port' => request()->input('mail_port', null),
      'mail_encryption' => request()->input('mail_encryption', null),
      'mail_username' => request()->input('mail_username', null),
      'mail_password' => request()->input('mail_password', null),
      'mail_mailgun_domain' => request()->input('mail_mailgun_domain', null),
      'mail_mailgun_secret' => request()->input('mail_mailgun_secret', null),
      'avangate_affiliate' => request()->input('avangate_affiliate', null),
      'avangate_key' => request()->input('avangate_key', null),
      'stripe_key' => request()->input('stripe_key', null),
      'stripe_secret' => request()->input('stripe_secret', null),
      'custom_affiliate_id' => request()->input('custom_affiliate_id', null),
      'user_query_parameter' => request()->input('user_query_parameter', null),
      'affiliate_query_parameter' => request()->input('affiliate_query_parameter', null),
      'payment_provider' => request()->input('payment_provider', null)
    );

    $rules = array(
      'domain' => 'required|unique:resellers',
      'name' => 'required|max:32',
      'support_email' => 'required|email'
    );

    $validator = \Validator::make($input, $rules);

    if($validator->fails())
    {
      $response = array(
        'type' => 'error', 
        'reset' => false, 
        'msg' => $validator->messages()->first()
      );
    }
    else
    {
      $reseller = new \App\Reseller;

      $reseller->api_token = str_random(60);
      $reseller->name = $input['name'];
      $reseller->support_email = $input['support_email'];
      $reseller->domain = $input['domain'];
      $reseller->logo = $input['logo'];
      $reseller->logo_square = $input['logo_square'];
      $reseller->favicon = $input['favicon'];
      $reseller->default_language = $input['language'];
      $reseller->default_timezone = $input['timezone'];
      $reseller->active = $input['active'];
      $reseller->mail_driver = $input['mail_driver'];
      $reseller->mail_from_name = $input['mail_from_name'];
      $reseller->mail_from_address = $input['mail_from_address'];
      $reseller->mail_host = $input['mail_host'];
      $reseller->mail_port = $input['mail_port'];
      $reseller->mail_encryption = $input['mail_encryption'];
      $reseller->mail_username = $input['mail_username'];
      $reseller->mail_password = $input['mail_password'];
      $reseller->mail_mailgun_domain = $input['mail_mailgun_domain'];
      $reseller->mail_mailgun_secret = $input['mail_mailgun_secret'];
      $reseller->avangate_affiliate = $input['avangate_affiliate'];
      $reseller->avangate_key = $input['avangate_key'];
      $reseller->stripe_key = $input['stripe_key'];
      $reseller->stripe_secret = $input['stripe_secret'];
      $reseller->settings = [
        'website_active' => $input['website_active'],
        'header_gradient_start' => $input['header_gradient_start'],
        'header_gradient_end' => $input['header_gradient_end'],
        'header_image' => $input['header_image'],
        'header_title' => $input['header_title'],
        'header_cta' => $input['header_cta'],
        'custom_affiliate_id' => $input['custom_affiliate_id'],
        'user_query_parameter' => $input['user_query_parameter'],
        'affiliate_query_parameter' => $input['affiliate_query_parameter'],
        'payment_provider' => $input['payment_provider']
      ];

      if($reseller->save())
      {
        // Update user
        $user = \App\User::find($input['user_id']);

        $user->is_reseller_id = $reseller->id;
        $user->reseller_id = $reseller->id;
        $user->role = 'reseller';
        $user->expires = null;
        $user->expires_reminders_sent = 0;
        $user->trial_ends_at = null;
        $user->trial_ends_reminders_sent = 0;
        $user->save();

        $response = array(
          'type' => 'success',
          'redir' => '#/admin/resellers'
        );
      }
      else
      {
        $response = array(
          'type' => 'error',
          'reset' => false, 
          'msg' => $reseller->errors()->first()
        );
      }
    }
    return response()->json($response);
  }

  /**
   * Save reseller changes
   */
  public function postReseller()
  {
    $sl = request()->input('sl', '');

    if($sl != '')
    {
      $qs = Core\Secure::string2array($sl);

      if (config('app.demo') && $qs['reseller_id'] == 1) {
        return response()->json([
          'type' => 'error',
          'reset' => false, 
          'msg' => "This is disabled in the demo"
        ]);
      }

      $reseller = \App\Reseller::find($qs['reseller_id']);

      $input = array(
        'timezone' => request()->input('timezone'),
        'language' => request()->input('language'),
        'name' => request()->input('name'),
        'support_email' => request()->input('support_email'),
        'domain' => request()->input('domain'),
        'logo' => request()->input('logo', null),
        'logo_square' => request()->input('logo_square', null),
        'favicon' => request()->input('favicon', null),
        'website_active' => (bool) request()->input('website_active', false),
        'header_gradient_start' => request()->input('header_gradient_start', null),
        'header_gradient_end' => request()->input('header_gradient_end', null),
        'header_image' => request()->input('header_image', null),
        'header_title' => request()->input('header_title', null),
        'header_cta' => request()->input('header_cta', null),
        'user_id' => request()->input('user_id'),
        'active' => (bool) request()->input('active', false),
        'mail_driver' => request()->input('mail_driver', null),
        'mail_from_name' => request()->input('mail_from_name', null),
        'mail_from_address' => request()->input('mail_from_address', null),
        'mail_host' => request()->input('mail_host', null),
        'mail_port' => request()->input('mail_port', null),
        'mail_encryption' => request()->input('mail_encryption', null),
        'mail_username' => request()->input('mail_username', null),
        'mail_password' => request()->input('mail_password', null),
        'mail_mailgun_domain' => request()->input('mail_mailgun_domain', null),
        'mail_mailgun_secret' => request()->input('mail_mailgun_secret', null),
        'avangate_affiliate' => request()->input('avangate_affiliate', null),
        'avangate_key' => request()->input('avangate_key', null),
        'stripe_key' => request()->input('stripe_key', null),
        'stripe_secret' => request()->input('stripe_secret', null),
        'custom_affiliate_id' => request()->input('custom_affiliate_id', null),
        'user_query_parameter' => request()->input('user_query_parameter', null),
        'affiliate_query_parameter' => request()->input('affiliate_query_parameter', null),
        'payment_provider' => request()->input('payment_provider', null)
      );

      $rules = array(
        'domain' => 'required|unique:resellers,domain,' . $qs['reseller_id'],
        'name' => 'required|max:32',
        'support_email' => 'required|email'
      );

      $validator = \Validator::make($input, $rules);

      if($validator->fails())
      {
        $response = array(
          'type' => 'error', 
          'reset' => false, 
          'msg' => $validator->messages()->first()
        );
      }
      else
      {
        $reseller->name = $input['name'];
        $reseller->support_email = $input['support_email'];
        $reseller->logo = $input['logo'];
        $reseller->logo_square = $input['logo_square'];
        $reseller->favicon = $input['favicon'];
        $reseller->default_language = $input['language'];
        $reseller->default_timezone = $input['timezone'];
        $reseller->mail_driver = $input['mail_driver'];
        $reseller->mail_from_name = $input['mail_from_name'];
        $reseller->mail_from_address = $input['mail_from_address'];
        $reseller->mail_host = $input['mail_host'];
        $reseller->mail_port = $input['mail_port'];
        $reseller->mail_encryption = $input['mail_encryption'];
        $reseller->mail_username = $input['mail_username'];
        $reseller->mail_password = $input['mail_password'];
        $reseller->mail_mailgun_domain = $input['mail_mailgun_domain'];
        $reseller->mail_mailgun_secret = $input['mail_mailgun_secret'];
        $reseller->avangate_affiliate = $input['avangate_affiliate'];
        $reseller->avangate_key = $input['avangate_key'];
        $reseller->stripe_key = $input['stripe_key'];
        $reseller->stripe_secret = $input['stripe_secret'];

        $reseller->settings = [
          'website_active' => $input['website_active'],
          'header_gradient_start' => $input['header_gradient_start'],
          'header_gradient_end' => $input['header_gradient_end'],
          'header_image' => $input['header_image'],
          'header_title' => $input['header_title'],
          'header_cta' => $input['header_cta'],
          'custom_affiliate_id' => $input['custom_affiliate_id'],
          'user_query_parameter' => $input['user_query_parameter'],
          'affiliate_query_parameter' => $input['affiliate_query_parameter'],
          'payment_provider' => $input['payment_provider']
        ];

        if ($qs['reseller_id'] > 1) {

          // Only update the domain if it's not the primary reseller to prevent a system lock out
          $reseller->domain = $input['domain'];

          // Only switch active if it's not the primary reseller
          $reseller->active = $input['active'];

          // Update old user
          $user = \App\User::where('is_reseller_id', $qs['reseller_id'])->first();

          if (! empty($user)) {
            $user->is_reseller_id = null;
            $user->role = 'user';
            $user->save();
          }

          // Update new user
          $user = \App\User::find($input['user_id']);

          $user->is_reseller_id = $qs['reseller_id'];
          $user->reseller_id = $qs['reseller_id'];
          $user->role = 'reseller';
          $user->expires = null;
          $user->expires_reminders_sent = 0;
          $user->trial_ends_at = null;
          $user->trial_ends_reminders_sent = 0;
          $user->save();
        }

        if($reseller->save()) {
          $response = [
            'type' => 'success',
            'reset' => false, 
            'msg' => trans('global.changes_saved')
          ];
          /*
          $response = [
            'redir' => '#/admin/resellers'
          ];*/
        } else {
          $response = [
            'type' => 'error',
            'reset' => false, 
            'msg' => $reseller->errors()->first()
          ];
        }
      }
      return response()->json($response);
    }
  }

  /**
   * Delete reseller
   */
  public function postResellerDelete()
  {
    $sl = request()->input('sl', '');

    if($sl != '') {
      $qs = Core\Secure::string2array($sl);
      $response = array('result' => 'success');

      if (config('app.demo') && $qs['reseller_id'] == 1) {
        return response()->json([
          'type' => 'error',
          'reset' => false, 
          'msg' => "This is disabled in the demo"
        ]);
      }

      $reseller = \App\Reseller::where('id', '>',  1)->where('id', '=',  $qs['reseller_id'])->first();

      if(! empty($reseller)) {
        $reseller = \App\Reseller::where('id', '=',  $qs['reseller_id'])->forceDelete();

        // Update reseller user
        $user = \App\User::where('is_reseller_id', $qs['reseller_id'])->first();
        if (! empty($user)) {
          $user->reseller_id = 1;
          $user->is_reseller_id = null;
          $user->role = 'user';
        }

        // Set all users from reseller to default reseller
        $update = \App\User::where('reseller_id', $qs['reseller_id'])->update(['reseller_id' => 1, 'is_reseller_id' => null, 'role' => 'user', 'plan_id' => null]);
      } else {
        $response = array('msg' => trans('global.cant_delete_owner'));
      }
    }
    return response()->json($response);
  }

  /**
   * Get reseller data
   */
  public function getResellerData(Request $request)
  {
    $order_by = $request->input('order.0.column', 0);
    $order = $request->input('order.0.dir', 'asc');
    $search = $request->input('search.regex', '');
    $q = $request->input('search.value', '');
    $start = $request->input('start', 0);
    $draw = $request->input('draw', 1);
    $length = $request->input('length', 10);
    $data = array();

    $aColumn = array('name', 'domain', 'user_name', 'user_email', 'created_at', 'active');

    if($q != '')
    {
      $count = \App\Reseller::leftJoin('users as u', 'resellers.id', '=', 'u.is_reseller_id')
        ->select(array('resellers.*', 'u.name as user_name', 'u.email as user_email'))
        ->where(function ($query) use($q) {
          $query->orWhere('resellers.name', 'like', '%' . $q . '%')
          ->orWhere('resellers.domain', 'like', '%' . $q . '%')
          ->orWhere('u.name', 'like', '%' . $q . '%')
          ->orWhere('u.email', 'like', '%' . $q . '%');
        })
        ->count();

      $oData = \App\Reseller::leftJoin('users as u', 'resellers.id', '=', 'u.is_reseller_id')
        ->orderBy($aColumn[$order_by], $order)
        ->select(array('resellers.*', 'u.name as user_name', 'u.email as user_email'))
        ->where(function ($query) use($q) {
          $query->orWhere('resellers.name', 'like', '%' . $q . '%')
          ->orWhere('resellers.domain', 'like', '%' . $q . '%')
          ->orWhere('u.name', 'like', '%' . $q . '%')
          ->orWhere('u.email', 'like', '%' . $q . '%');
        })
        ->take($length)->skip($start)->get();
    }
    else
    {
      $count = \App\Reseller::leftJoin('users as u', 'resellers.id', '=', 'u.is_reseller_id')->count();
      $oData = \App\Reseller::leftJoin('users as u', 'resellers.id', '=', 'u.is_reseller_id')->select(array('resellers.*', 'u.name as user_name', 'u.email as user_email'))->orderBy($aColumn[$order_by], $order)->take($length)->skip($start)->get();
    }

    if($length == -1) $length = $count;

    $recordsTotal = $count;
    $recordsFiltered = $count;

    foreach($oData as $row) {
      $undeletable = ($row->id == 1) ? 1 : 0;
      $favicon = ($row->favicon == null) ? url('assets/branding/favicon.ico') : $row->favicon;

      $data[] = array(
        'DT_RowId' => 'row_' . $row->id,
        'name' => $row->name,
        'domain' => $row->domain,
        'user_name' => $row->user_name,
        'user_email' => $row->user_email,
        'favicon' => $favicon,
        'active' => $row->active,
        'created_at' => $row->created_at->timezone(\Auth::user()->timezone)->format('Y-m-d H:i:s'),
        'sl' => Core\Secure::array2string(array('reseller_id' => $row->id)),
        'undeletable' => $undeletable
      );
    }

    $response = array(
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsFiltered,
      'data' => $data
    );

    echo json_encode($response);
  }
}
<?php
namespace Modules\EmailCampaigns\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model {

  protected $casts = [
    'meta' => 'json'
  ];

  /**
   * Dynamically set a model's table.
   *
   * @param  $table
   * @return void
   */
  public function setTable($table) {
    $this->table = $table;
    return $this;
  }

  public function email() {
    return $this->belongsTo('EmailCampaigns\Email', 'email_campaign_id');
  }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignProduct extends Model
{
    // Specify the table name
    protected $table = 'campaign_product';
    
    protected $fillable = [
        'campaign_id', 'product_id'
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

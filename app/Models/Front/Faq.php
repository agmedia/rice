<?php

namespace App\Models\Front;

use App\Models\Back\Settings\FaqTranslation;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{

    /**
     * @var string
     */
    protected $table = 'faq';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var string
     */
    protected $locale = 'en';


    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->locale = current_locale();
    }


    /**
     * @param null  $lang
     * @param false $all
     *
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\HasOne|object|null
     */
    public function translation($lang = null, bool $all = false)
    {
        if ($lang) {
            return $this->hasOne(FaqTranslation::class, 'faq_id')->where('lang', $lang)->first();
        }

        if ($all) {
            return $this->hasMany(FaqTranslation::class, 'faq_id');
        }

        return $this->hasOne(FaqTranslation::class, 'faq_id')->where('lang', $this->locale);
    }


    /**
     * @return string
     */
    public function getTitleAttribute()
    {
        return $this->translation->title;
    }


    /**
     * @return string
     */
    public function getDescriptionAttribute()
    {
        return $this->translation->description;
    }

}

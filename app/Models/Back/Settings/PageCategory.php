<?php

namespace App\Models\Back\Settings;

use App\Models\Back\Catalog\Category;
use Illuminate\Database\Eloquent\Model;

class PageCategory extends Model
{

    /**
     * @var string $table
     */
    protected $table = 'page_category';

    /**
     * @var array $guarded
     */
    protected $guarded = [];


    /**
     * Update Page categories.
     *
     * @param array $categories
     * @param int   $page_id
     *
     * @return array
     */
    public static function storeData(array $categories, int $page_id): array
    {
        $created = [];
        self::where('page_id', $page_id)->delete();

        foreach ($categories as $category) {
            $cat = Category::find($category);

            if ($cat) {
                if ($cat->parent_id) {
                    $created[] = self::insert([
                        'page_id'     => $page_id,
                        'category_id' => $cat->parent_id
                    ]);
                }

                $created[] = self::insert([
                    'page_id'     => $page_id,
                    'category_id' => $category
                ]);
            }
        }

        return $created;
    }
}

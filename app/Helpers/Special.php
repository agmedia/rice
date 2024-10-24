<?php

namespace App\Helpers;

use App\Models\Back\Catalog\Product\ProductCategory;
use App\Models\Front\Catalog\ProductAction;
use App\Models\Front\Catalog\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class Special
{

    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected $user;

    /**
     * @var Product|null
     */
    protected $product;

    /**
     * @var ProductAction|null
     */
    protected $action;

    /**
     * @var Collection|null
     */
    private $active_actions;


    /**
     * @param Product|null       $product
     * @param ProductAction|null $product_action
     */
    public function __construct(Product $product = null)
    {
        $this->user    = Auth::user();
        $this->product = $product;
    }


    /**
     * @return ProductAction|null
     */
    public function getAction(): ?ProductAction
    {
        return $this->action;
    }


    /**
     * @param string $group
     *
     * @return ProductAction|null
     */
    public function resolveAction(string $group = ''): ?ProductAction
    {
        return $this->setupAvailableActions($group)->getAction();
    }


    /**
     * @param ProductAction|null $product_action
     *
     * @return float|int
     */
    public function getDiscountPrice(ProductAction|null $product_action = null): float|int
    {
        $action = $product_action ?: $this->action;

        if ( ! $action) {
            return $this->product->price;
        }

        if ($this->isProductOnAction($action)) {
            return Helper::calculateDiscountPrice($this->product->price, $action->discount, $action->type);
        }

        return $this->product->price;
    }


    /**
     * @param ProductAction|null $product_action
     *
     * @return bool
     */
    public function checkCoupon(ProductAction|null $product_action = null): bool
    {
        $action             = $product_action ?: $this->action;
        $coupon_session_key = config('session.cart') . '_coupon';
        $coupon             = false;

        if ( ! $action || ($action && ! $action->coupon)) {
            $coupon = true;
        }

        if (isset($action->status) && $action->status) {
            if ((isset($action->coupon) && $action->coupon) && session()->has($coupon_session_key) && session($coupon_session_key) == $action->coupon) {
                $coupon = true;
            }
        }

        return $coupon;
    }


    /**
     * @param ProductAction|null $product_action
     *
     * @return bool
     */
    public function checkDates(ProductAction|null $product_action = null): bool
    {
        $action = $product_action ?: $this->action;

        if ( ! $action) {
            return false;
        }

        $from = now()->subDay();
        $to   = now()->addDay();

        if ($action->date_start && $action->date_start != '0000-00-00 00:00:00') {
            $from = Carbon::make($action->date_start);
        }
        if ($action->date_end && $action->date_end != '0000-00-00 00:00:00') {
            $to = Carbon::make($action->date_end);
        }

        if ($from <= now() && now() <= $to) {
            return true;
        }

        return false;
    }


    /**
     * @param ProductAction|null $product_action
     *
     * @return bool
     */
    public function isProductOnAction(ProductAction|null $product_action = null): bool
    {
        $action = $product_action ?: $this->action;

        if ($this->isActionOnAllProducts($action)) {
            return true;
        }

        $ids = $this->getActionProductsList($action);

        if (in_array($this->product->id, $ids)) {
            return true;
        }

        return false;
    }


    /**
     * @return bool
     */
    public function isActionOnAllProducts(ProductAction|null $product_action = null): bool
    {
        if (in_array($product_action->group, ['all'])) {
            return true;
        }

        return false;
    }


    /**
     * @param ProductAction|null $product_action
     *
     * @return array
     */
    public function getActionProductsList(ProductAction|null $product_action = null): array
    {
        $action = $product_action ?: $this->action;

        $ids = explode(',', substr(str_replace('"', '', $action->links), 1, -1));

        if ($action->group == 'product') {
            return $ids;
        }

        if ($action->group == 'category') {
            return ProductCategory::query()->whereIn('category_id', $ids)->pluck('product_id')->unique()->toArray();
        }

        if ($action->group == 'brand') {
            return Product::query()->whereIn('brand_id', $ids)->pluck('id')->unique()->toArray();
        }

        return [];
    }


    /**
     * @return mixed
     */
    public function getBestAction()
    {
        if ($this->active_actions->count() > 1) {
            if ($this->active_actions->first()->group != 'total') {
                $price          = $this->product->price;
                $best_action_id = 0;

                foreach ($this->active_actions as $action) {
                    $coupon_ok = $this->checkCoupon($action);
                    $dates_ok  = $this->checkDates($action);

                    if ($coupon_ok && $dates_ok) {
                        $temp_price = $this->getDiscountPrice($action);

                        if ($price > $temp_price) {
                            $price          = $temp_price;
                            $best_action_id = $action->id;
                        }
                    }
                }

                return $this->active_actions->where('id', $best_action_id)->first();
            }
            else {
                $best_action_id = 0;

                foreach ($this->active_actions as $action) {
                    $coupon_ok = $this->checkCoupon($action);
                    $dates_ok  = $this->checkDates($action);

                    if ($coupon_ok && $dates_ok) {
                        $best_action_id = $action->id;
                    }
                }

                return $this->active_actions->where('id', $best_action_id)->first();
            }
        }

        return $this->active_actions->first();
    }


    /**
     * @param string $group
     *
     * @return Special
     */
    private function setupAvailableActions(string $group = ''): Special
    {
        if ($group != '') {
            $this->active_actions = ProductAction::query()->where('status', 1)->where('group', $group)->active()->get();
        } else {
            $this->active_actions = ProductAction::query()->where('status', 1)->where('group', '!=', 'total')->active()->get();
        }

        if ($this->active_actions->count()) {
            $this->action = $this->getBestAction();
        }

        return $this;
    }

}
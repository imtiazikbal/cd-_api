<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\AccountLedger
 *
 * @property int $id
 * @property string $name
 * @property int $shop_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $type
 * @method static \Illuminate\Database\Eloquent\Builder|AccountLedger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountLedger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountLedger query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountLedger whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountLedger whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountLedger whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountLedger whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountLedger whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountLedger whereUpdatedAt($value)
 */
	class AccountLedger extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AccountPayor
 *
 * @property int $id
 * @property string $name
 * @property int $shop_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $type
 * @method static \Illuminate\Database\Eloquent\Builder|AccountPayor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountPayor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountPayor query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountPayor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountPayor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountPayor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountPayor whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountPayor whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountPayor whereUpdatedAt($value)
 */
	class AccountPayor extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Accountsmodule
 *
 * @property int $id
 * @property int $shop_id
 * @property int|null $ledger_id
 * @property float $amount
 * @property int|null $payor_id
 * @property int|null $payment_id
 * @property string $payment_type
 * @property string|null $description
 * @property string $date
 * @property string $time
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $bill_no
 * @property int|null $balance
 * @property-read \App\Models\AccountLedger|null $ledger
 * @property-read \App\Models\PaymentMethod|null $payment
 * @property-read \App\Models\AccountPayor|null $payor
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule query()
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule whereBillNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule whereLedgerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule wherePayorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Accountsmodule whereUpdatedAt($value)
 */
	class Accountsmodule extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ActiveTheme
 *
 * @property int $id
 * @property int $shop_id
 * @property int $theme_id
 * @property int|null $page_id
 * @property int|null $checkout_form_id
 * @property int|null $footer_id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CheckFormDesign|null $Checkout
 * @property-read \App\Models\Footer|null $Footer
 * @property-read \App\Models\Media|null $media
 * @property-read \App\Models\Page|null $page
 * @property-read \App\Models\Shop|null $shop
 * @property-read \App\Models\Theme|null $theme
 * @method static \Illuminate\Database\Eloquent\Builder|ActiveTheme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActiveTheme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActiveTheme query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActiveTheme whereCheckoutFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActiveTheme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActiveTheme whereFooterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActiveTheme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActiveTheme wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActiveTheme whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActiveTheme whereThemeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActiveTheme whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActiveTheme whereUpdatedAt($value)
 */
	class ActiveTheme extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Addons
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $amount
 * @property string $payment_type free,paid
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Media|null $addons_image
 * @method static \Illuminate\Database\Eloquent\Builder|Addons newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Addons newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Addons query()
 * @method static \Illuminate\Database\Eloquent\Builder|Addons whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addons whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addons whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addons whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addons whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addons wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addons whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addons whereUpdatedAt($value)
 */
	class Addons extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Attachment
 *
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string|null $path
 * @property string $type
 * @property int $size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereUuid($value)
 */
	class Attachment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Attribute
 *
 * @property int $id
 * @property int|null $shop_id
 * @property string $key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AttributeValue> $attribute_values
 * @property-read int|null $attribute_values_count
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereUpdatedAt($value)
 */
	class Attribute extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttributeValue
 *
 * @property int $id
 * @property int $attribute_id
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Attribute|null $attribute
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue whereAttributeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttributeValue whereValue($value)
 */
	class AttributeValue extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Banner
 *
 * @property int $id
 * @property string $image
 * @property string|null $link
 * @property string $user_id
 * @property string $shop_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $banner_image
 * @property-read int|null $banner_image_count
 * @method static \Illuminate\Database\Eloquent\Builder|Banner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner query()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereUserId($value)
 */
	class Banner extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Category
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property int $shop_id
 * @property int $parent_id
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read \App\Models\Media|null $category_image
 * @property-read Category|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $subcategory
 * @property-read int|null $subcategory_count
 * @method static \Database\Factories\CategoryFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CheckFormDesign
 *
 * @property int $id
 * @property string $name
 * @property string $thumnail
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CheckFormDesign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CheckFormDesign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CheckFormDesign query()
 * @method static \Illuminate\Database\Eloquent\Builder|CheckFormDesign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckFormDesign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckFormDesign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckFormDesign whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckFormDesign whereThumnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CheckFormDesign whereUpdatedAt($value)
 */
	class CheckFormDesign extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CourierStatus
 *
 * @property int $id
 * @property int $order_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CourierStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourierStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourierStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|CourierStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourierStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourierStatus whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourierStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourierStatus whereUpdatedAt($value)
 */
	class CourierStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CustomerFaq
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $answer1
 * @property string|null $answer2
 * @property string|null $answer3
 * @property string|null $answer4
 * @property string|null $answer5
 * @property string|null $answer6
 * @property string|null $answer7
 * @property string|null $answer8
 * @property string|null $answer9
 * @property string|null $answer10
 * @property string|null $answer11
 * @property string|null $answer12
 * @property string|null $answer13
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereAnswer1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereAnswer10($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereAnswer11($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereAnswer12($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereAnswer13($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereAnswer2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereAnswer3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereAnswer4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereAnswer5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereAnswer6($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereAnswer7($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereAnswer8($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereAnswer9($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerFaq whereUserId($value)
 */
	class CustomerFaq extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CustomerInfo
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $phone
 * @property int $merchant_id
 * @property string $type
 * @property string|null $address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \App\Models\Shop $shop
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInfo whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInfo whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInfo wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInfo whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerInfo whereUserId($value)
 */
	class CustomerInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Footer
 *
 * @property int $id
 * @property string $name
 * @property string $thumnail
 * @property int $status
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Footer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Footer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Footer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Footer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Footer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Footer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Footer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Footer whereThumnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Footer whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Footer whereUpdatedAt($value)
 */
	class Footer extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Media
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property int $parent_id
 * @property string $parent_type
 * @property string $type
 * @property string $url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Filesystem $disk
 * @property-read string $local
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $parent
 * @method static \Illuminate\Database\Eloquent\Builder|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media query()
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereParentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUpdatedAt($value)
 */
	class Media extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MerchantCourier
 *
 * @property int $id
 * @property string $shop_id
 * @property string $provider
 * @property string $status
 * @property string $config
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantCourier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantCourier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantCourier query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantCourier whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantCourier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantCourier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantCourier whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantCourier whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantCourier whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantCourier whereUpdatedAt($value)
 */
	class MerchantCourier extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MerchantInfo
 *
 * @property int $id
 * @property int $user_id
 * @property string $short_address
 * @property string|null $address
 * @property string $fb_page
 * @property string $short_description
 * @property string|null $description
 * @property string|null $other_info
 * @property float $allocate_balance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantInfo whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantInfo whereAllocateBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantInfo whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantInfo whereFbPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantInfo whereOtherInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantInfo whereShortAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantInfo whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantInfo whereUserId($value)
 */
	class MerchantInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MerchantToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $ip
 * @property string $browser
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantToken whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantToken whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantToken whereUserId($value)
 */
	class MerchantToken extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MyAddons
 *
 * @property int $id
 * @property int $shop_id
 * @property int $addons_id
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Addons> $addons
 * @property-read int|null $addons_count
 * @property-read \App\Models\Addons|null $addons_details
 * @property-read \App\Models\Media|null $addons_image_details
 * @method static \Illuminate\Database\Eloquent\Builder|MyAddons newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MyAddons newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MyAddons query()
 * @method static \Illuminate\Database\Eloquent\Builder|MyAddons whereAddonsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyAddons whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyAddons whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyAddons whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyAddons whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MyAddons whereUpdatedAt($value)
 */
	class MyAddons extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Note
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Note newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Note newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Note query()
 */
	class Note extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $order_status
 * @property string $order_type
 * @property string $order_no
 * @property string $shop_id
 * @property string $customer_name
 * @property string $phone
 * @property string $address
 * @property string $created_at
 * @property string $updated_at
 * @property array $order_details
 * @property string $delivery_location
 * @property object $pricing
 * @property object $config
 * @property object $courier
 * @property bool $cod
 * @property string|null $status_update_date
 * @property string $cronjob_status n=normal, p=processing
 * @property string|null $courier_status
 * @property int|null $order_perm_status
 * @property string|null $visitor_id
 * @property string|null $identify_otp
 * @property int $otp_verified
 * @property int $otp_sent
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderDate> $order_dates
 * @property-read int|null $order_dates_count
 * @property-read int|null $order_details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderStatus> $status
 * @property-read int|null $status_count
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order shopWiseOrderCount(string $shop_id, string $start_date, $end_date)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCourierStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCronjobStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeliveryLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIdentifyOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderPermStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOtpSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOtpVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatusUpdateDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereVisitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order withoutTrashed()
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderConfig
 *
 * @property int $id
 * @property int $order_id
 * @property int $invoice_print
 * @property int $courier_entry
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderConfig whereCourierEntry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderConfig whereInvoicePrint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderConfig whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderConfig whereUpdatedAt($value)
 */
	class OrderConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * Class OrderCourier
 *
 * @package App\Models
 * @property int $id
 * @property int $order_id
 * @property string $tracking_code
 * @property string $status
 * @property string $provider
 * @property string|null $consignment_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCourier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCourier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCourier query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCourier whereConsignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCourier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCourier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCourier whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCourier whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCourier whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCourier whereTrackingCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCourier whereUpdatedAt($value)
 */
	class OrderCourier extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderDate
 *
 * @property int $id
 * @property int $order_id
 * @property string $type
 * @property string $date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDate query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDate whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDate whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDate whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDate whereUpdatedAt($value)
 */
	class OrderDate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderDetails
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $product_qty
 * @property int $shipping_cost
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $unit_price
 * @property int|null $variant
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductVariation|null $variation
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereProductQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereShippingCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDetails whereVariant($value)
 */
	class OrderDetails extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderNote
 *
 * @property int $id
 * @property int $order_id
 * @property string $type
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderNote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderNote whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderNote whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderNote whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderNote whereUpdatedAt($value)
 */
	class OrderNote extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderPricing
 *
 * @property int $advanced
 * @property int $due
 * @property int $shipping_cost
 * @property int $id
 * @property int $order_id
 * @property int $grand_total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $discount
 * @property string $discount_type
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPricing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPricing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPricing query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPricing whereAdvanced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPricing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPricing whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPricing whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPricing whereDue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPricing whereGrandTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPricing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPricing whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPricing whereShippingCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPricing whereUpdatedAt($value)
 */
	class OrderPricing extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderStatus
 *
 * @property int $id
 * @property int $order_id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereUpdatedAt($value)
 */
	class OrderStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OtherScript
 *
 * @property int $id
 * @property int $shop_id
 * @property string|null $gtm_head
 * @property string|null $gtm_body
 * @property string|null $google_analytics
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OtherScript newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OtherScript newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OtherScript query()
 * @method static \Illuminate\Database\Eloquent\Builder|OtherScript whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OtherScript whereGoogleAnalytics($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OtherScript whereGtmBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OtherScript whereGtmHead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OtherScript whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OtherScript whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OtherScript whereUpdatedAt($value)
 */
	class OtherScript extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Package
 *
 * @property int $id
 * @property string $name
 * @property int $min_order
 * @property int $max_order
 * @property string $duration
 * @property int $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Package newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package query()
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereMaxOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereMinOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereUpdatedAt($value)
 */
	class Package extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Page
 *
 * @property int $id
 * @property int $user_id
 * @property int $shop_id
 * @property int|null $product_id
 * @property string $title
 * @property string $slug
 * @property string|null $page_content
 * @property string|null $descriptions
 * @property int $theme
 * @property string|null $video_link
 * @property int $status
 * @property string $logo
 * @property string|null $fb
 * @property string|null $twitter
 * @property string|null $linkedin
 * @property string|null $instagram
 * @property string|null $youtube
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $footer_text_color
 * @property string|null $footer_link_color
 * @property string|null $footer_b_color
 * @property string|null $footer_heading_color
 * @property string|null $checkout_text_color
 * @property string|null $checkout_link_color
 * @property string|null $checkout_b_color
 * @property string|null $checkout_button_color
 * @property string|null $checkout_button_text_color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $order_title
 * @property string|null $checkout_button_text
 * @property string|null $note
 * @property-read \App\Models\ActiveTheme|null $Footer
 * @property-read \App\Models\ActiveTheme|null $activeFooter
 * @property-read \App\Models\ActiveTheme|null $activeTheme
 * @property-read \App\Models\Media|null $page_reviews
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Shop|null $shop
 * @property-read \App\Models\Theme|null $themes
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereCheckoutBColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereCheckoutButtonColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereCheckoutButtonText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereCheckoutButtonTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereCheckoutLinkColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereCheckoutTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereDescriptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereFb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereFooterBColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereFooterHeadingColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereFooterLinkColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereFooterTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereOrderTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page wherePageContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereVideoLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereYoutube($value)
 */
	class Page extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentMethod
 *
 * @property int $id
 * @property string $name
 * @property string $shop_id
 * @property int $status
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereUpdatedAt($value)
 */
	class PaymentMethod extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Product
 *
 * @property int $id
 * @property int $category_id
 * @property int $shop_id
 * @property string $product_name
 * @property string $product_code
 * @property int $product_qty
 * @property string $slug
 * @property float $price
 * @property string $delivery_charge
 * @property int $inside_dhaka
 * @property int $outside_dhaka
 * @property float $discount
 * @property string|null $short_description
 * @property string|null $long_description
 * @property int $status
 * @property int $sub_area_charge
 * @property string|null $default_delivery_location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $discount_type
 * @property mixed|null $attributes
 * @property-read \App\Models\Media|null $main_image
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $other_images
 * @property-read int|null $other_images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductVariation> $variations
 * @property-read int|null $variations_count
 * @method static \Database\Factories\ProductFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDefaultDeliveryLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeliveryCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereInsideDhaka($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereLongDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereOutsideDhaka($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSubAreaCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductVariation
 *
 * @property int $id
 * @property int $product_id
 * @property string $variant
 * @property int $price
 * @property int $quantity
 * @property string $code
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Media|null $media
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereVariant($value)
 */
	class ProductVariation extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\RoleFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SalesTarget
 *
 * @property int $id
 * @property int $user_id
 * @property int $shop_id
 * @property int $daily
 * @property int $weekly
 * @property int $monthly
 * @property int $custom
 * @property string|null $from_date
 * @property string|null $to_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SalesTarget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesTarget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesTarget query()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesTarget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesTarget whereCustom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesTarget whereDaily($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesTarget whereFromDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesTarget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesTarget whereMonthly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesTarget whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesTarget whereToDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesTarget whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesTarget whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesTarget whereWeekly($value)
 */
	class SalesTarget extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ShippingSetting
 *
 * @property int $id
 * @property float $inside
 * @property float $outside
 * @property float $subarea
 * @property int $shop_id
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShippingSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShippingSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShippingSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShippingSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShippingSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShippingSetting whereInside($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShippingSetting whereOutside($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShippingSetting whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShippingSetting whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShippingSetting whereSubarea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShippingSetting whereUpdatedAt($value)
 */
	class ShippingSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Shop
 *
 * @package App\Models
 * @property string $shop_id
 * @property int $id
 * @property string $name
 * @property string $domain
 * @property string|null $address
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $shop_meta_title
 * @property string|null $shop_meta_description
 * @property string|null $fb_pixel
 * @property string|null $c_status
 * @property string|null $c_api
 * @property string|null $test_event
 * @property string|null $domain_verify
 * @property string|null $domain_request
 * @property string|null $domain_status
 * @property int $sms_sent
 * @property string $sms_balance
 * @property int $courier_balance
 * @property int $user_id
 * @property string|null $about_us
 * @property string|null $privacy_policy
 * @property string|null $tos
 * @property string|null $fb
 * @property string|null $twitter
 * @property string|null $linkedin
 * @property string|null $instagram
 * @property string|null $youtube
 * @property string|null $order_sms {"cancelled":"1","confirmed":"1","shipped":"1","return":"1","delivered":"1","pending":"1","hold_on":"1"}
 * @property string|null $default_delivery_location
 * @property int|null $order_perm_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $multipage_color
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MyAddons> $addons_info
 * @property-read int|null $addons_info_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Banner> $banner
 * @property-read int|null $banner_count
 * @property-read \App\Models\User $merchant
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \App\Models\OtherScript|null $otherScript
 * @property-read \App\Models\Media|null $shop_favicon
 * @property-read \App\Models\Media|null $shop_logo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Slider> $slider
 * @property-read int|null $slider_count
 * @method static \Illuminate\Database\Eloquent\Builder|Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop query()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereAboutUs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCourierBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDefaultDeliveryLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDomainRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDomainStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDomainVerify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereFb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereFbPixel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereMultipageColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereOrderPermStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereOrderSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop wherePrivacyPolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShopMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereShopMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereSmsBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereSmsSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereTestEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereTos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereYoutube($value)
 */
	class Shop extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Slider
 *
 * @property int $id
 * @property string|null $image
 * @property string|null $link
 * @property string $shop_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $slider_image
 * @property-read int|null $slider_image_count
 * @method static \Illuminate\Database\Eloquent\Builder|Slider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider query()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereUserId($value)
 */
	class Slider extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SmsTemplate
 *
 * @property int $id
 * @property string|null $message
 * @property int $shop_id
 * @property string $module
 * @property string|null $type
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereUpdatedAt($value)
 */
	class SmsTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Status
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status query()
 */
	class Status extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Subscription
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $invoice_num
 * @property int $amount
 * @property string $gateway
 * @property string|null $sub_gateway
 * @property string $gateway_trxid
 * @property mixed $api_response
 * @property string|null $next_due_date
 * @property string $status
 * @property string $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereApiResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereGatewayTrxid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereInvoiceNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereNextDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereSubGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUserId($value)
 */
	class Subscription extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SupportTicket
 *
 * @property int $id
 * @property string $uuid
 * @property string $ticket_id
 * @property string $subject
 * @property string $content
 * @property int $user_id
 * @property string|null $shop_name
 * @property int|null $staff_id
 * @property int|null $attachment_id
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property-read \App\Models\Attachment|null $attachment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketComment> $comments
 * @property-read int|null $comments_count
 * @property-read mixed $shop_id
 * @property-read \App\Models\User $merchant
 * @property-read \App\Models\User|null $staff
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket multiSearch($request)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereAttachmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereShopName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereUuid($value)
 */
	class SupportTicket extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Theme
 *
 * @property int $id
 * @property string $name
 * @property string|null $url
 * @property string $type landing/multiple
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $theme_name
 * @property-read \App\Models\Media|null $media
 * @property-read \App\Models\Page|null $page
 * @method static \Illuminate\Database\Eloquent\Builder|Theme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Theme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Theme query()
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereThemeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereUrl($value)
 */
	class Theme extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ThemeEdit
 *
 * @property int $id
 * @property string $title
 * @property string|null $menu
 * @property string $content
 * @property string $logo
 * @property string $page
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Theme> $theme
 * @property string $type
 * @property int $shop_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ThemeImage> $gallery
 * @property-read int|null $gallery_count
 * @property-read int|null $theme_count
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeEdit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeEdit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeEdit query()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeEdit whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeEdit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeEdit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeEdit whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeEdit whereMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeEdit wherePage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeEdit whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeEdit whereTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeEdit whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeEdit whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeEdit whereUpdatedAt($value)
 */
	class ThemeEdit extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ThemeImage
 *
 * @property int $id
 * @property int $theme_edit_id
 * @property string $type
 * @property string $file_name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\ThemeEdit|null $theme
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeImage whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeImage whereThemeEditId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeImage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeImage whereUpdatedAt($value)
 */
	class ThemeImage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TicketComment
 *
 * @property string $id
 * @property int $ticket_id
 * @property int $user_id
 * @property int|null $attachment_id
 * @property string $content
 * @property int|null $shop_id
 * @property string|null $shop_name
 * @property string $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Attachment|null $attachment
 * @property-read \App\Models\SupportTicket|null $support_ticket
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereAttachmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereShopName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereUserId($value)
 */
	class TicketComment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Transaction
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $trxid
 * @property string $invoice_num
 * @property int|null $addons_id
 * @property string $type
 * @property int $amount
 * @property string|null $response
 * @property string $status
 * @property string|null $gateway
 * @property string|null $sub_gateway
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $package_id
 * @property string|null $due_date
 * @property int|null $order_count
 * @property-read \App\Models\Addons|null $addons
 * @property-read \App\Models\Package|null $package
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAddonsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereInvoiceNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereOrderCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereSubGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTrxid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUserId($value)
 */
	class Transaction extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property string $email
 * @property string $name
 * @property string $phone
 * @property string $status
 * @property string $payment_status
 * @property int $id
 * @property string|null $address
 * @property string|null $password
 * @property string $role
 * @property string|null $otp
 * @property string|null $api_token
 * @property string|null $email_verified_at
 * @property string|null $phone_verified_at
 * @property string|null $remember_token
 * @property string $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $next_due_date
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \App\Models\CustomerInfo|null $customer_info
 * @property-read string $avatar
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MerchantToken> $merchant_tokens
 * @property-read int|null $merchant_tokens_count
 * @property-read \App\Models\MerchantInfo|null $merchantinfo
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Shop|null $shop
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SupportTicket> $support_ticket
 * @property-read int|null $support_ticket_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNextDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoneVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\WebsiteSetting
 *
 * @property int $id
 * @property int $cash_on_delivery
 * @property int $advanced_payment
 * @property int $shipped_date_status
 * @property int|null $hold_on
 * @property int|null $invoice_id
 * @property string|null $custom_domain
 * @property string|null $shop_name
 * @property string|null $shop_address
 * @property string|null $website_shop_id
 * @property int $user_id
 * @property int $shop_id
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Media|null $website_shop_logo
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereAdvancedPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereCashOnDelivery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereCustomDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereHoldOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereShippedDateStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereShopAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereShopName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebsiteSetting whereWebsiteShopId($value)
 */
	class WebsiteSetting extends \Eloquent {}
}


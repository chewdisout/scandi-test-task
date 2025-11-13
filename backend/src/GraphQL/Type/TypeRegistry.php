<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

class TypeRegistry
{
    private static ?CategoryType $category = null;
    private static ?ProductType $product = null;
    private static ?CurrencyType $currency = null;
    private static ?PriceType $price = null;
    private static ?AttributeSetType $attributeSet = null;
    private static ?AttributeItemType $attributeItem = null;
    private static ?SelectedAttributeType $selectedAttribute = null;
    private static ?OrderItemType $orderItem = null;
    private static ?OrderType $order = null;

    public static function category(): CategoryType { return self::$category ??= new CategoryType(); }
    public static function product(): ProductType { return self::$product ??= new ProductType(); }
    public static function currency(): CurrencyType { return self::$currency ??= new CurrencyType(); }
    public static function price(): PriceType { return self::$price ??= new PriceType(); }
    public static function attributeSet(): AttributeSetType { return self::$attributeSet ??= new AttributeSetType(); }
    public static function attributeItem(): AttributeItemType { return self::$attributeItem ??= new AttributeItemType(); }

    public static function selectedAttribute(): SelectedAttributeType
    {
        return self::$selectedAttribute ??= new SelectedAttributeType();
    }

    public static function orderItem(): OrderItemType
    {
        return self::$orderItem ??= new OrderItemType();
    }

    public static function order(): OrderType
    {
        return self::$order ??= new OrderType();
    }
}

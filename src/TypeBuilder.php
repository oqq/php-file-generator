<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

final readonly class TypeBuilder
{
    /**
     * @template Tk of string
     * @template Tv
     *
     * @param array<Tk, Type<Tv>> $elements
     *
     * @return Type<array<Tk, Tv>>
     */
    public static function shape(array $elements): Type
    {
        return new Type\ShapeType($elements);
    }

    /**
     * @return Type<non-empty-string>
     */
    public static function nonEmptyString(): Type
    {
        return new Type\NonEmptyStringType();
    }

    /**
     * @template T
     *
     * @param Type<T> $innerType
     *
     * @return Type<T|null>
     */
    public static function nullable(Type $innerType): Type
    {
        return new Type\NullableType($innerType);
    }

    /**
     * @template T
     *
     * @param Type<T> $innerType
     *
     * @return Type<list<T>>
     */
    public static function list(Type $innerType): Type
    {
        return new Type\ListType($innerType);
    }

    /**
     * @template T
     *
     * @param Type<T> $innerType
     *
     * @return Type<T>
     */
    public static function optional(Type $innerType): Type
    {
        return new Type\OptionalType($innerType);
    }

    /**
    * @template T as object
    *
    * @param class-string<T> $className
    *
    * @return Type<T>
    */
    public static function instanceOf(string $className): Type\InstanceOfType
    {
        return new Type\InstanceOfType($className);
    }

    /**
     * @return Type<bool>
     */
    public static function boolean(): Type
    {
        return new Type\BooleanType();
    }

    /**
     * @return Type<non-negative-int>
     */
    public static function natural(): Type
    {
        return new Type\NaturalType();
    }

    /**
     * @return Type<string>
     */
    public static function string(): Type
    {
        return new Type\StringType();
    }

    /**
     * @return Type<non-empty-string>
     */
    public static function uuid(): Type
    {
        return new Type\UuidType();
    }

    /**
     * @return Type<Type<array-key>, Type>
     */
    public static function dict(Type $keyType, Type $valueType): Type
    {
        return new Type\DictType($keyType, $valueType);
    }

    /**
     * @return Type<iterable<Type>, Type>
     */
    public static function iterable(Type $keyType, Type $valueType): Type
    {
        return new Type\IterableType($keyType, $valueType);
    }

    /**
     * @return Type<array-key>
     */
    public static function arrayKey(): Type
    {
        return new Type\ArrayKeyType();
    }

    /**
     * @return Type<mixed>
     */
    public static function mixed(): Type
    {
        return new Type\MixedType();
    }
}


/**
 * @return string
 */
public static function createUniquePoolingKeyFor<?php echo $columnPhpName; ?>($value)
{
    return md5('<?php echo $keyPrefix; ?>'.$value);
}

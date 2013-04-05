
/**
 * @return <?php echo $objectClassName; ?>|null
 */
public function findOneBy<?php echo $columnPhpName; ?>($value)
{
    $key = static::createUniquePoolingKeyFor<?php echo $columnPhpName; ?>($value);

    if ((null !== $obj = <?php echo $peerClassName; ?>::getInstanceFromPool($key)) and !$this->formatter) {
        return $obj;
    }

    $obj = parent::findOneBy<?php echo $columnPhpName; ?>($value);
    if ($obj) {
        <?php echo $peerClassName; ?>::addInstanceToPool($obj, $key);
    }

    return $obj;
}

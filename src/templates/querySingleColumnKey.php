
/**
 * @return <?php echo $objectClassName; ?>
 */
public function findOneBy<?php echo $columnPhpName; ?>($value)
{
    $key = md5('<?php echo $keyPrefix; ?>'.$value);

    if ((null !== $obj = <?php echo $peerClassName; ?>::getInstanceFromPool($key)) and !$this->formatter) {
        return $obj;
    }

    $obj = parent::findOneBy<?php echo $columnPhpName; ?>($value);

    <?php echo $peerClassName; ?>::addInstanceToPool($obj, (string) $key);

    return $obj;
}

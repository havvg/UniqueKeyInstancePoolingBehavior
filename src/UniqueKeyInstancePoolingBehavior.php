<?php

class UniqueKeyInstancePoolingBehavior extends Behavior
{
    /**
     * @param QueryBuilder $builder
     *
     * @return string
     */
    public function queryMethods($builder)
    {
        $script = '';

        /* @var $eachKey Unique */
        foreach ($this->getTable()->getUnices() as $eachKey) {
            /* @var $columns Column[] */
            $columns = $eachKey->getColumns();

            if (1 === count($columns)) {
                $script .= $this->renderTemplate('querySingleColumnKey', array(
                    'keyPrefix' => sprintf('unique_%s_', $columns[0]),
                    'columnPhpName' => $this->getTable()->getColumn($columns[0])->getPhpName(),
                    'objectClassName' => $builder->getStubObjectBuilder()->getClassname(),
                    'peerClassName' => $builder->getStubPeerBuilder()->getClassname(),
                ));
            }

            // TODO: add handling of multi column keys.
        }

        return $script;
    }
}

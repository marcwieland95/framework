<?php

namespace Assely\Metabox;

use Assely\Foundation\Factory;
use Assely\Contracts\Singularity\Model\ModelInterface;

class MetaboxFactory extends Factory
{
    /**
     * Create metabox.
     *
     * @param  \Assely\Contracts\Singularity\Model\ModelInterface $model
     * @param  array $belongsTo
     *
     * @return \Assely\Metabox\MetaboxSingularity
     */
    public function create(ModelInterface $model, $belongsTo)
    {
        $metabox = $this->container->make(MetaboxSingularity::class);

        $metabox
            ->setModel($model)
            ->setBelongsTo($belongsTo)
            ->boot();

        return $metabox;
    }
}

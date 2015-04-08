<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchBundle\Tests\app\fixture\Acme\TestBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\ElasticsearchBundle\Document\AbstractDocument;
use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use ONGR\ElasticsearchBundle\Document\DocumentTrait;

/**
 * Class ColorDocument.
 *
 * @ES\Document(type="color")
 */
class ColorDocument extends AbstractDocument
{
    /**
     * @var CdnObject[]
     *
     * @ES\Property(
     *      type="object",
     *      name="disabled_cdn",
     *      enabled=false,
     *      multiple=true,
     *      objectName="AcmeTestBundle:CdnObject"
     * )
     */
    public $disabledCdn;

    /**
     * @var CdnObject[]
     *
     * @ES\Property(
     *      type="object",
     *      name="enabled_cdn",
     *      multiple=true,
     *      objectName="AcmeTestBundle:CdnObject"
     * )
     */
    public $enabledCdn;
}

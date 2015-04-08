<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchBundle\Tests\Functional\DSL\Aggregation;

use ONGR\ElasticsearchBundle\DSL\Aggregation\ExtendedStatsAggregation;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;

/**
 * Functional tests for extended stats aggregation. Elasticsearch version >= 1.5.0.
 */
class ExtendedStatsAggregationTest extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getIgnoredVersions()
    {
        return [
            ['1.5.0', '<'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'product' => [
                    [
                        '_id' => 1,
                        'title' => 'foo',
                        'price' => 10.45,
                    ],
                    [
                        '_id' => 2,
                        'title' => 'bar',
                        'price' => 32,
                    ],
                    [
                        '_id' => 3,
                        'title' => 'pizza',
                        'price' => 15.1,
                    ],
                ],
            ],
        ];
    }

    /**
     * Test for extended stats aggregation.
     */
    public function testExtendedStatsAggregation()
    {
        /** @var Repository $repo */
        $repo = $this->getManager()->getRepository('AcmeTestBundle:Product');

        $aggregation = new ExtendedStatsAggregation('test_agg');
        $aggregation->setField('price');

        $search = $repo->createSearch()->addAggregation($aggregation);
        $results = $repo->execute($search, Repository::RESULTS_RAW);

        $expectedMin = 10.449999809265137;

        $this->assertArrayHasKey('aggregations', $results, 'results array should have aggregations key');
        $this->assertEquals($expectedMin, $results['aggregations'][$aggregation->getName()]['min']);
    }

    /**
     * Test for extended stats aggregation with sigma set.
     */
    public function testExtendedStatsAggregationWithSigmaSet()
    {
        /** @var Repository $repo */
        $repo = $this->getManager()->getRepository('AcmeTestBundle:Product');

        $aggregation = new ExtendedStatsAggregation('test_agg');
        $aggregation->setField('price');
        $aggregation->setSigma(1);

        $search = $repo->createSearch()->addAggregation($aggregation);
        $results = $repo->execute($search, Repository::RESULTS_RAW);

        $expectedResult = [
            'agg_test_agg' => [
                'count' => 3,
                'min' => 10.449999809265137,
                'max' => 32.0,
                'avg' => 19.183333396911621,
                'sum' => 57.550000190734863,
                'sum_of_squares' => 1361.2125075340273,
                'variance' => 85.737222294277672,
                'std_deviation' => 9.2594396317637742,
                'std_deviation_bounds' => ['upper' => 28.442773028675397, 'lower' => 9.9238937651478469],
                'min_as_string' => '10.449999809265137',
                'max_as_string' => '32.0',
                'avg_as_string' => '19.18333339691162',
                'sum_as_string' => '57.55000019073486',
                'sum_of_squares_as_string' => '1361.2125075340273',
                'variance_as_string' => '85.73722229427767',
                'std_deviation_as_string' => '9.259439631763774',
                'std_deviation_bounds_as_string' => ['upper' => '28.442773028675397', 'lower' => '9.9238937651478469'],
            ],
        ];

        $this->assertEquals($expectedResult, $results['aggregations']);
    }

    /**
     * Test for extended stats aggregation with script set.
     */
    public function testExtendedStatsAggregationWithScriptSet()
    {
        $repo = $this->getManager()->getRepository('AcmeTestBundle:Product');
        $aggregation = new ExtendedStatsAggregation('test_agg');
        $aggregation->setScript("doc['product.price'].value * 1.5");
        $search = $repo->createSearch()->addAggregation($aggregation);
        $results = $repo->execute($search, Repository::RESULTS_RAW);
        $expectedMin = 15.674999713897705;

        $this->assertEquals($expectedMin, $results['aggregations'][$aggregation->getName()]['min']);
    }
}

<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon Framework.
 *
 * (c) Phalcon Team <team@phalconphp.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Phalcon\Test\Integration\Mvc\Model;

use IntegrationTester;
use Phalcon\Test\Fixtures\Traits\DiTrait;
use Phalcon\Test\Models;
use Phalcon\Mvc\Model\Exception;

/**
 * Class GetRelatedCest
 */
class GetRelatedCest
{
    use DiTrait;

    public function _before(IntegrationTester $I)
    {
        $this->setNewFactoryDefault();
        $this->setDiMysql();
    }

    /**
     * Tests Phalcon\Mvc\Model :: getRelated()
     *
     * @param IntegrationTester $I
     *
     * @author Balázs Németh <https://github.com/zsilbi>
     * @since  2019-05-07
     */
    public function mvcModelGetRelated(IntegrationTester $I)
    {
        $I->wantToTest('Mvc\Model - getRelated()');

        /**
         * Belongs-to relationship
         */
        $robotPart = Models\RobotsParts::findFirst();

        $part = $robotPart->getRelated('part');

        $I->assertInstanceOf(
            Models\Parts::class,
            $part
        );

        $nonExistentPart = $robotPart->getRelated(
            'part',
            [
                'id < 0',
                'order' => 'id DESC'
            ]
        );

        $I->assertFalse($nonExistentPart);

        /**
         * Testing has-one relationship
         */
        $customer = Models\Customers::findFirst();

        $user = $customer->getRelated('user');

        $I->assertInstanceOf(
            Models\Users::class,
            $user
        );

        $nonExistentUser = $customer->getRelated(
            'user',
            [
                'id < 0',
                'order' => 'id DESC'
            ]
        );

        $I->assertFalse($nonExistentUser);

        /**
         * Has-many relationship
         */
        $robot = Models\Robots::findFirst();

        $robotParts = $robot->getRelated('robotsParts');

        $I->assertInstanceOf(
            'Phalcon\Mvc\Model\Resultset\Simple',
            $robotParts
        );

        $nonExistentRobotParts = $robot->getRelated(
            'robotsParts',
            [
                'id < 0',
                'order' => 'id DESC'
            ]
        );

        $I->assertEquals(
            0,
            $nonExistentRobotParts->count()
        );

        /**
         * Non-existent relationship
         */
        $I->expectThrowable(
            new Exception(
                sprintf(
                    "There is no defined relations for the model '%s' using alias '%s'",
                    Models\Robots::class,
                    'nonExistentRelation'
                )
            ),
            function () use ($robot) {
                $robot->getRelated('nonExistentRelation');
            }
        );
    }
}

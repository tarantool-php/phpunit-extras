<?php

/**
 * This file is part of the tarantool/phpunit-extras package.
 *
 * (c) Eugene Leonovich <gen.work@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tarantool\PhpUnit\Expectation;

use Tarantool\PhpUnit\Expectation\ExpressionContext\RequestCounter;

trait RequestExpectations
{
    use AuthRequestExpectations;
    use CallRequestExpectations;
    use DeleteRequestExpectations;
    use EvalRequestExpectations;
    use ExecuteRequestExpectations;
    use PrepareRequestExpectations;
    use InsertRequestExpectations;
    use ReplaceRequestExpectations;
    use SelectRequestExpectations;
    use UpdateRequestExpectations;
    use UpsertRequestExpectations;

    /** @var RequestCounter|null */
    private $requestCounter;

    final protected function getRequestCounter() : RequestCounter
    {
        return $this->requestCounter ?:
            $this->requestCounter = new RequestCounter();
    }

    public function expectNoRequestToBeCalled() : void
    {
        $this->expectAuthRequestToBeNeverCalled();
        $this->expectCallRequestToBeNeverCalled();
        $this->expectDeleteRequestToBeNeverCalled();
        $this->expectEvalRequestToBeNeverCalled();
        $this->expectExecuteRequestToBeNeverCalled();
        $this->expectPrepareRequestToBeNeverCalled();
        $this->expectInsertRequestToBeNeverCalled();
        $this->expectReplaceRequestToBeNeverCalled();
        $this->expectSelectRequestToBeNeverCalled();
        $this->expectUpdateRequestToBeNeverCalled();
        $this->expectUpsertRequestToBeNeverCalled();
    }
}

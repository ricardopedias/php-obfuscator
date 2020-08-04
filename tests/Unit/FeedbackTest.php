<?php

declare(strict_types=1);

namespace PhpObfuscator\Tests\Unit;

use PhpObfuscator\Feedback;
use PhpObfuscator\ObfuscateFile;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FeedbackTest extends TestCase
{
    /** @test */
    public function addRuntimeMessage()
    {
        $feedback = new Feedback();

        $this->assertInstanceOf(Feedback::class, $feedback->addRuntimeMessage('aaa'));
        $this->assertInstanceOf(Feedback::class, $feedback->addRuntimeMessage('bbb'));
        $this->assertInstanceOf(Feedback::class, $feedback->addRuntimeMessage('ccc'));
    }

     /** @test */
     public function noRuntimeMessage()
     {
         $feedback = new Feedback();
         $this->assertEquals('',  $feedback->getLastRuntimeMessage());
     }

    /** @test */
    public function lastRuntimeMessage()
    {
        $feedback = new Feedback();

        $this->assertInstanceOf(Feedback::class, $feedback->addRuntimeMessage('aaa'));
        $this->assertInstanceOf(Feedback::class, $feedback->addRuntimeMessage('bbb'));
        $this->assertInstanceOf(Feedback::class, $feedback->addRuntimeMessage('ccc'));

        $this->assertCount(3, $feedback->getRuntimeMessages());

        $this->assertEquals($feedback->getLastRuntimeMessage(), 'ccc');

        $feedback->addRuntimeMessage('ddd');
        $this->assertEquals($feedback->getLastRuntimeMessage(), 'ddd');
    }

    /** @test */
    public function addErrorMessage()
    {
        $feedback = new Feedback();

        $this->assertInstanceOf(Feedback::class, $feedback->addErrorMessage('aaa'));
        $this->assertInstanceOf(Feedback::class, $feedback->addErrorMessage('bbb'));
        $this->assertInstanceOf(Feedback::class, $feedback->addErrorMessage('ccc'));

        $this->assertCount(3, $feedback->getErrorMessages());
        $this->assertEquals($feedback->getErrorMessages()[0], 'aaa');
        $this->assertEquals($feedback->getErrorMessages()[1], 'bbb');
        $this->assertEquals($feedback->getErrorMessages()[2], 'ccc');
    }

     /** @test */
     public function noErrorMessage()
     {
         $feedback = new Feedback();
         $this->assertEquals('',  $feedback->getLastErrorMessage());
     }

    /** @test */
    public function addErrorMessageException()
    {
        $feedback = new Feedback();

        $this->expectException(RuntimeException::class);

        $feedback->enableThrowErrors();
        $feedback->addErrorMessage('aaa');
    }

    /** @test */
    public function lastErrorMessage()
    {
        $feedback = new Feedback();

        $this->assertInstanceOf(Feedback::class, $feedback->addErrorMessage('aaa'));
        $this->assertInstanceOf(Feedback::class, $feedback->addErrorMessage('bbb'));
        $this->assertInstanceOf(Feedback::class, $feedback->addErrorMessage('ccc'));

        $this->assertCount(3, $feedback->getErrorMessages());
        $this->assertEquals($feedback->getLastErrorMessage(), 'ccc');

        $feedback->addErrorMessage('ddd');
        $this->assertEquals($feedback->getLastErrorMessage(), 'ddd');
    }
}

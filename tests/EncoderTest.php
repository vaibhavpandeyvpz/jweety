<?php

/*
 * This file is part of vaibhavpandeyvpz/jweety package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the LICENSE file.
 */

namespace Jweety;

use PHPUnit\Framework\TestCase;

/**
 * Class EncoderTest
 * @package Jweety
 */
class EncoderTest extends TestCase
{
    public function testParse()
    {
        $encoder = new Encoder('12345678');
        $claims = $encoder->parse('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJleHAiOjE1MDU5Nzg3NzksImlhdCI6MTUwNTk3NTE3OSwiaXNzIjoiaHR0cDpcL1wvbG9jYWxob3N0IiwianRpIjoiZGJhYzQ2YzQtNTY4Zi00NDBlLTk0N2ItZTA2ZTU2NDY3YTUyIiwibmJmIjoxNTA1OTc2Mzc5LCJzdWIiOiJsb2dpbiJ9.fhL4x5RqJDskFr1y3eJ1XoAukGAULfFoCr7yAXGxRCE', false);
        $this->assertNotNull($claims->aud);
        $this->assertEquals('http://localhost', $claims->aud);
        $this->assertNotNull($claims->exp);
        $this->assertEquals(1505978779, $claims->exp);
        $this->assertNotNull($claims->iat);
        $this->assertEquals(1505975179, $claims->iat);
        $this->assertNotNull($claims->iss);
        $this->assertEquals('http://localhost', $claims->iss);
        $this->assertNotNull($claims->jti);
        $this->assertEquals('dbac46c4-568f-440e-947b-e06e56467a52', $claims->jti);
        $this->assertNotNull($claims->nbf);
        $this->assertEquals(1505976379, $claims->nbf);
        $this->assertNotNull($claims->sub);
        $this->assertEquals('login', $claims->sub);
    }

    public function testParseHs384()
    {
        $encoder = new Encoder('12345678');
        $claims = $encoder->parse('eyJhbGciOiJIUzM4NCIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJleHAiOjE1MDU5Nzg3NzksImlhdCI6MTUwNTk3NTE3OSwiaXNzIjoiaHR0cDpcL1wvbG9jYWxob3N0IiwianRpIjoiZGJhYzQ2YzQtNTY4Zi00NDBlLTk0N2ItZTA2ZTU2NDY3YTUyIiwibmJmIjoxNTA1OTc2Mzc5LCJzdWIiOiJsb2dpbiJ9.87suyXdLR1BBDI3038rQKqHczkjbutZvVFpcBZHI-5WBGBRXR61V1DSufryM4yUP', false);
        $this->assertNotNull($claims->aud);
        $this->assertEquals('http://localhost', $claims->aud);
        $this->assertNotNull($claims->exp);
        $this->assertEquals(1505978779, $claims->exp);
        $this->assertNotNull($claims->iat);
        $this->assertEquals(1505975179, $claims->iat);
        $this->assertNotNull($claims->iss);
        $this->assertEquals('http://localhost', $claims->iss);
        $this->assertNotNull($claims->jti);
        $this->assertEquals('dbac46c4-568f-440e-947b-e06e56467a52', $claims->jti);
        $this->assertNotNull($claims->nbf);
        $this->assertEquals(1505976379, $claims->nbf);
        $this->assertNotNull($claims->sub);
        $this->assertEquals('login', $claims->sub);
    }

    public function testParseHs512()
    {
        $encoder = new Encoder('12345678');
        $claims = $encoder->parse('eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJleHAiOjE1MDU5Nzg3NzksImlhdCI6MTUwNTk3NTE3OSwiaXNzIjoiaHR0cDpcL1wvbG9jYWxob3N0IiwianRpIjoiZGJhYzQ2YzQtNTY4Zi00NDBlLTk0N2ItZTA2ZTU2NDY3YTUyIiwibmJmIjoxNTA1OTc2Mzc5LCJzdWIiOiJsb2dpbiJ9.6aqoZnFBCCQtQELt5LF2E0x40gjYOZF4NsL7IdP3b3jq2IP_wHezzBfftquJW_k4lwTEmSAsDQAw1YSOR6mtCQ', false);
        $this->assertNotNull($claims->aud);
        $this->assertEquals('http://localhost', $claims->aud);
        $this->assertNotNull($claims->exp);
        $this->assertEquals(1505978779, $claims->exp);
        $this->assertNotNull($claims->iat);
        $this->assertEquals(1505975179, $claims->iat);
        $this->assertNotNull($claims->iss);
        $this->assertEquals('http://localhost', $claims->iss);
        $this->assertNotNull($claims->jti);
        $this->assertEquals('dbac46c4-568f-440e-947b-e06e56467a52', $claims->jti);
        $this->assertNotNull($claims->nbf);
        $this->assertEquals(1505976379, $claims->nbf);
        $this->assertNotNull($claims->sub);
        $this->assertEquals('login', $claims->sub);
    }

    public function testParseMalformed()
    {
        $encoder = new Encoder('12345678');
        $this->expectException('InvalidArgumentException');
        $encoder->parse('not-a-jwt-at-all');
    }

    public function testParseBadSignature()
    {
        $encoder = new Encoder('12345678');
        $this->expectException('RuntimeException');
        $encoder->parse('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJleHAiOjE1MDU5Nzg3NzksImlhdCI6MTUwNTk3NTE3OSwiaXNzIjoiaHR0cDpcL1wvbG9jYWxob3N0IiwianRpIjoiZGJhYzQ2YzQtNTY4Zi00NDBlLTk0N2ItZTA2ZTU2NDY3YTUyIiwibmJmIjoxNTA1OTc2Mzc5LCJzdWIiOiJsb2dpbiJ9.fhL4x5RqJDskFr1y3eJ1XoAukGAULfFoCr7yAXGxRCE_extra');
    }

    public function testParseUnsupportedAlgorithm()
    {
        $encoder = new Encoder('12345678');
        $this->expectException('InvalidArgumentException');
        $encoder->parse('eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIn0.eyJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJleHAiOjE1MDU5Nzg3NzksImlhdCI6MTUwNTk3NTE3OSwiaXNzIjoiaHR0cDpcL1wvbG9jYWxob3N0IiwianRpIjoiZGJhYzQ2YzQtNTY4Zi00NDBlLTk0N2ItZTA2ZTU2NDY3YTUyIiwibmJmIjoxNTA1OTc2Mzc5LCJzdWIiOiJsb2dpbiJ9.fhL4x5RqJDskFr1y3eJ1XoAukGAULfFoCr7yAXGxRCE_extra');
    }

    public function testStringify()
    {
        $encoder = new Encoder('12345678');
        $claims = array(
            'aud' => 'http://localhost',
            'exp' => 1505978779,
            'iat' => 1505975179,
            'iss' => 'http://localhost',
            'jti' => 'dbac46c4-568f-440e-947b-e06e56467a52',
            'nbf' => 1505976379,
            'sub' => 'login',
        );
        $token = $encoder->stringify($claims);
        $this->assertEquals('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJleHAiOjE1MDU5Nzg3NzksImlhdCI6MTUwNTk3NTE3OSwiaXNzIjoiaHR0cDpcL1wvbG9jYWxob3N0IiwianRpIjoiZGJhYzQ2YzQtNTY4Zi00NDBlLTk0N2ItZTA2ZTU2NDY3YTUyIiwibmJmIjoxNTA1OTc2Mzc5LCJzdWIiOiJsb2dpbiJ9.fhL4x5RqJDskFr1y3eJ1XoAukGAULfFoCr7yAXGxRCE', $token);
    }

    public function testStringifyHs384()
    {
        $encoder = new Encoder('12345678');
        $claims = array(
            'aud' => 'http://localhost',
            'exp' => 1505978779,
            'iat' => 1505975179,
            'iss' => 'http://localhost',
            'jti' => 'dbac46c4-568f-440e-947b-e06e56467a52',
            'nbf' => 1505976379,
            'sub' => 'login',
        );
        $token = $encoder->stringify($claims, 'HS384');
        $this->assertEquals('eyJhbGciOiJIUzM4NCIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJleHAiOjE1MDU5Nzg3NzksImlhdCI6MTUwNTk3NTE3OSwiaXNzIjoiaHR0cDpcL1wvbG9jYWxob3N0IiwianRpIjoiZGJhYzQ2YzQtNTY4Zi00NDBlLTk0N2ItZTA2ZTU2NDY3YTUyIiwibmJmIjoxNTA1OTc2Mzc5LCJzdWIiOiJsb2dpbiJ9.87suyXdLR1BBDI3038rQKqHczkjbutZvVFpcBZHI-5WBGBRXR61V1DSufryM4yUP', $token);
    }

    public function testStringifyHs512()
    {
        $encoder = new Encoder('12345678');
        $claims = array(
            'aud' => 'http://localhost',
            'exp' => 1505978779,
            'iat' => 1505975179,
            'iss' => 'http://localhost',
            'jti' => 'dbac46c4-568f-440e-947b-e06e56467a52',
            'nbf' => 1505976379,
            'sub' => 'login',
        );
        $token = $encoder->stringify($claims, 'HS512');
        $this->assertEquals('eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJleHAiOjE1MDU5Nzg3NzksImlhdCI6MTUwNTk3NTE3OSwiaXNzIjoiaHR0cDpcL1wvbG9jYWxob3N0IiwianRpIjoiZGJhYzQ2YzQtNTY4Zi00NDBlLTk0N2ItZTA2ZTU2NDY3YTUyIiwibmJmIjoxNTA1OTc2Mzc5LCJzdWIiOiJsb2dpbiJ9.6aqoZnFBCCQtQELt5LF2E0x40gjYOZF4NsL7IdP3b3jq2IP_wHezzBfftquJW_k4lwTEmSAsDQAw1YSOR6mtCQ', $token);
    }

    public function testStringifyDisallowedAlgorithm()
    {
        $encoder = new Encoder('12345678');
        $this->expectException('InvalidArgumentException');
        $encoder->stringify(array('sub' => 'login'), 'none');
    }

    public function testAssert()
    {
        $claims = (object)array('exp' => time() + (60 * 1000), 'iat' => time() - (60 * 1000), 'nbf' => time() - 1000,);
        $encoder = new Encoder(null);
        $encoder->assert($claims);
        $this->assertTrue(true);
    }

    public function testAssertExp()
    {
        $claims = (object)array('exp' => time() - (60 * 1000));
        $encoder = new Encoder(null);
        $this->expectException('InvalidArgumentException');
        $encoder->assert($claims);
    }

    public function testAssertIat()
    {
        $claims = (object)array('iat' => time() + (60 * 1000));
        $encoder = new Encoder(null);
        $this->expectException('InvalidArgumentException');
        $encoder->assert($claims);
    }

    public function testAssertNbf()
    {
        $claims = (object)array('nbf' => time() + (60 * 1000));
        $encoder = new Encoder(null);
        $this->expectException('InvalidArgumentException');
        $encoder->assert($claims);
    }
}

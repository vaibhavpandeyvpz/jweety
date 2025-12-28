<?php

declare(strict_types=1);

/*
 * This file is part of vaibhavpandeyvpz/jweety package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the LICENSE file.
 */

namespace Jweety;

use Jweety\Exception\InvalidSignatureException;
use Jweety\Exception\InvalidTokenException;
use Jweety\Exception\TokenExpiredException;
use Jweety\Exception\UnsupportedAlgorithmException;
use PHPUnit\Framework\TestCase;

/**
 * Class EncoderTest
 */
class EncoderTest extends TestCase
{
    public function test_parse(): void
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

    public function test_parse_hs384(): void
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

    public function test_parse_hs512(): void
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

    public function test_parse_malformed(): void
    {
        $encoder = new Encoder('12345678');
        $this->expectException(InvalidTokenException::class);
        $encoder->parse('not-a-jwt-at-all');
    }

    public function test_parse_bad_signature(): void
    {
        $encoder = new Encoder('12345678');
        // Create a valid token
        $validToken = $encoder->stringify(['sub' => 'test']);
        $parts = explode('.', $validToken);
        // Replace signature with a valid base64 but wrong signature
        $wrongSignature = Encoder::encode('wrong-signature-bytes');
        $invalidToken = $parts[0].'.'.$parts[1].'.'.$wrongSignature;

        $this->expectException(InvalidSignatureException::class);
        $encoder->parse($invalidToken);
    }

    public function test_parse_unsupported_algorithm(): void
    {
        $encoder = new Encoder('12345678');
        $this->expectException(UnsupportedAlgorithmException::class);
        $encoder->parse('eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIn0.eyJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJleHAiOjE1MDU5Nzg3NzksImlhdCI6MTUwNTk3NTE3OSwiaXNzIjoiaHR0cDpcL1wvbG9jYWxob3N0IiwianRpIjoiZGJhYzQ2YzQtNTY4Zi00NDBlLTk0N2ItZTA2ZTU2NDY3YTUyIiwibmJmIjoxNTA1OTc2Mzc5LCJzdWIiOiJsb2dpbiJ9.fhL4x5RqJDskFr1y3eJ1XoAukGAULfFoCr7yAXGxRCE_extra');
    }

    public function test_stringify(): void
    {
        $encoder = new Encoder('12345678');
        $claims = [
            'aud' => 'http://localhost',
            'exp' => 1505978779,
            'iat' => 1505975179,
            'iss' => 'http://localhost',
            'jti' => 'dbac46c4-568f-440e-947b-e06e56467a52',
            'nbf' => 1505976379,
            'sub' => 'login',
        ];
        $token = $encoder->stringify($claims);
        $this->assertEquals('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJleHAiOjE1MDU5Nzg3NzksImlhdCI6MTUwNTk3NTE3OSwiaXNzIjoiaHR0cDpcL1wvbG9jYWxob3N0IiwianRpIjoiZGJhYzQ2YzQtNTY4Zi00NDBlLTk0N2ItZTA2ZTU2NDY3YTUyIiwibmJmIjoxNTA1OTc2Mzc5LCJzdWIiOiJsb2dpbiJ9.fhL4x5RqJDskFr1y3eJ1XoAukGAULfFoCr7yAXGxRCE', $token);
    }

    public function test_stringify_with_enum(): void
    {
        $encoder = new Encoder('12345678');
        $claims = [
            'aud' => 'http://localhost',
            'exp' => 1505978779,
            'iat' => 1505975179,
            'iss' => 'http://localhost',
            'jti' => 'dbac46c4-568f-440e-947b-e06e56467a52',
            'nbf' => 1505976379,
            'sub' => 'login',
        ];
        $token = $encoder->stringify($claims, Algorithm::HS256);
        $this->assertEquals('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJleHAiOjE1MDU5Nzg3NzksImlhdCI6MTUwNTk3NTE3OSwiaXNzIjoiaHR0cDpcL1wvbG9jYWxob3N0IiwianRpIjoiZGJhYzQ2YzQtNTY4Zi00NDBlLTk0N2ItZTA2ZTU2NDY3YTUyIiwibmJmIjoxNTA1OTc2Mzc5LCJzdWIiOiJsb2dpbiJ9.fhL4x5RqJDskFr1y3eJ1XoAukGAULfFoCr7yAXGxRCE', $token);
    }

    public function test_stringify_hs384(): void
    {
        $encoder = new Encoder('12345678');
        $claims = [
            'aud' => 'http://localhost',
            'exp' => 1505978779,
            'iat' => 1505975179,
            'iss' => 'http://localhost',
            'jti' => 'dbac46c4-568f-440e-947b-e06e56467a52',
            'nbf' => 1505976379,
            'sub' => 'login',
        ];
        $token = $encoder->stringify($claims, 'HS384');
        $this->assertEquals('eyJhbGciOiJIUzM4NCIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJleHAiOjE1MDU5Nzg3NzksImlhdCI6MTUwNTk3NTE3OSwiaXNzIjoiaHR0cDpcL1wvbG9jYWxob3N0IiwianRpIjoiZGJhYzQ2YzQtNTY4Zi00NDBlLTk0N2ItZTA2ZTU2NDY3YTUyIiwibmJmIjoxNTA1OTc2Mzc5LCJzdWIiOiJsb2dpbiJ9.87suyXdLR1BBDI3038rQKqHczkjbutZvVFpcBZHI-5WBGBRXR61V1DSufryM4yUP', $token);
    }

    public function test_stringify_hs512(): void
    {
        $encoder = new Encoder('12345678');
        $claims = [
            'aud' => 'http://localhost',
            'exp' => 1505978779,
            'iat' => 1505975179,
            'iss' => 'http://localhost',
            'jti' => 'dbac46c4-568f-440e-947b-e06e56467a52',
            'nbf' => 1505976379,
            'sub' => 'login',
        ];
        $token = $encoder->stringify($claims, 'HS512');
        $this->assertEquals('eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJleHAiOjE1MDU5Nzg3NzksImlhdCI6MTUwNTk3NTE3OSwiaXNzIjoiaHR0cDpcL1wvbG9jYWxob3N0IiwianRpIjoiZGJhYzQ2YzQtNTY4Zi00NDBlLTk0N2ItZTA2ZTU2NDY3YTUyIiwibmJmIjoxNTA1OTc2Mzc5LCJzdWIiOiJsb2dpbiJ9.6aqoZnFBCCQtQELt5LF2E0x40gjYOZF4NsL7IdP3b3jq2IP_wHezzBfftquJW_k4lwTEmSAsDQAw1YSOR6mtCQ', $token);
    }

    public function test_stringify_disallowed_algorithm(): void
    {
        $encoder = new Encoder('12345678');
        $this->expectException(UnsupportedAlgorithmException::class);
        $encoder->stringify(['sub' => 'login'], 'none');
    }

    public function test_assert(): void
    {
        $claims = (object) [
            'exp' => time() + (60 * 1000),
            'iat' => time() - (60 * 1000),
            'nbf' => time() - 1000,
        ];
        $encoder = new Encoder('');
        $encoder->assert($claims);
        $this->assertTrue(true);
    }

    public function test_assert_exp(): void
    {
        $claims = (object) ['exp' => time() - (60 * 1000)];
        $encoder = new Encoder('');
        $this->expectException(TokenExpiredException::class);
        $encoder->assert($claims);
    }

    public function test_assert_iat(): void
    {
        $claims = (object) ['iat' => time() + (60 * 1000)];
        $encoder = new Encoder('');
        $this->expectException(InvalidTokenException::class);
        $encoder->assert($claims);
    }

    public function test_assert_nbf(): void
    {
        $claims = (object) ['nbf' => time() + (60 * 1000)];
        $encoder = new Encoder('');
        $this->expectException(InvalidTokenException::class);
        $encoder->assert($claims);
    }

    // Parse edge cases

    public function test_parse_empty_string(): void
    {
        $encoder = new Encoder('12345678');
        $this->expectException(InvalidTokenException::class);
        $encoder->parse('');
    }

    public function test_parse_one_part(): void
    {
        $encoder = new Encoder('12345678');
        $this->expectException(InvalidTokenException::class);
        $encoder->parse('only-one-part');
    }

    public function test_parse_two_parts(): void
    {
        $encoder = new Encoder('12345678');
        $this->expectException(InvalidTokenException::class);
        $encoder->parse('part1.part2');
    }

    public function test_parse_invalid_base64_header(): void
    {
        $encoder = new Encoder('12345678');
        $this->expectException(InvalidTokenException::class);
        $encoder->parse('invalid!!!.eyJzdWIiOiJ0ZXN0In0.signature');
    }

    public function test_parse_invalid_json_header(): void
    {
        $encoder = new Encoder('12345678');
        $this->expectException(InvalidTokenException::class);
        // Base64 encoded "not json"
        $encoder->parse('bm90IGpzb24.eyJzdWIiOiJ0ZXN0In0.signature');
    }

    public function test_parse_header_not_array(): void
    {
        $encoder = new Encoder('12345678');
        $this->expectException(InvalidTokenException::class);
        // Base64 encoded "string" (not an object/array)
        $encoder->parse('InN0cmluZyI.eyJzdWIiOiJ0ZXN0In0.signature');
    }

    public function test_parse_header_missing_alg(): void
    {
        $encoder = new Encoder('12345678');
        $this->expectException(InvalidTokenException::class);
        // Base64 encoded {"typ":"JWT"} (missing alg)
        $encoder->parse('eyJ0eXAiOiJKV1QifQ.eyJzdWIiOiJ0ZXN0In0.signature');
    }

    public function test_parse_invalid_base64_payload(): void
    {
        $encoder = new Encoder('12345678');
        $this->expectException(InvalidTokenException::class);
        $encoder->parse('eyJhbGciOiJIUzI1NiJ9.invalid!!!.signature');
    }

    public function test_parse_payload_not_object(): void
    {
        $encoder = new Encoder('12345678');
        // Create a valid token first
        $validToken = $encoder->stringify(['sub' => 'test']);
        $parts = explode('.', $validToken);

        // Replace payload with array JSON (invalid - should be object)
        $invalidPayload = Encoder::encode(json_encode([1, 2, 3], JSON_THROW_ON_ERROR));
        // Create new signature for modified payload
        $newSignature = hash_hmac('sha256', $parts[0].'.'.$invalidPayload, '12345678', true);
        $newSignatureEncoded = Encoder::encode($newSignature);
        $invalidToken = $parts[0].'.'.$invalidPayload.'.'.$newSignatureEncoded;

        $this->expectException(InvalidTokenException::class);
        $encoder->parse($invalidToken);
    }

    public function test_parse_wrong_key(): void
    {
        $encoder1 = new Encoder('key1');
        $token = $encoder1->stringify(['sub' => 'test']);

        $encoder2 = new Encoder('key2');
        $this->expectException(InvalidSignatureException::class);
        $encoder2->parse($token);
    }

    public function test_parse_with_assert_true(): void
    {
        $encoder = new Encoder('12345678');
        $claims = ['sub' => 'test', 'exp' => time() - 3600]; // Expired
        $token = $encoder->stringify($claims);

        $this->expectException(TokenExpiredException::class);
        $encoder->parse($token, true);
    }

    public function test_parse_with_assert_false(): void
    {
        $encoder = new Encoder('12345678');
        $claims = ['sub' => 'test', 'exp' => time() - 3600]; // Expired
        $token = $encoder->stringify($claims);

        // Should not throw exception when assert=false
        $parsed = $encoder->parse($token, false);
        $this->assertEquals('test', $parsed->sub);
    }

    // Stringify edge cases

    public function test_stringify_empty_claims(): void
    {
        $encoder = new Encoder('12345678');
        // Use empty object instead of empty array to ensure it parses as object
        $token = $encoder->stringify((object) []);
        $this->assertNotEmpty($token);
        $parsed = $encoder->parse($token, false);
        $this->assertIsObject($parsed);
    }

    public function test_stringify_claims_as_object(): void
    {
        $encoder = new Encoder('12345678');
        $claims = (object) ['sub' => 'test', 'iat' => time()];
        $token = $encoder->stringify($claims);
        $parsed = $encoder->parse($token, false);
        $this->assertEquals('test', $parsed->sub);
    }

    public function test_stringify_with_nested_structures(): void
    {
        $encoder = new Encoder('12345678');
        $claims = [
            'sub' => 'test',
            'data' => ['nested' => ['value' => 123]],
            'list' => [1, 2, 3],
        ];
        $token = $encoder->stringify($claims);
        $parsed = $encoder->parse($token, false);
        $this->assertEquals('test', $parsed->sub);
        $this->assertEquals(123, $parsed->data->nested->value);
    }

    public function test_stringify_with_unicode(): void
    {
        $encoder = new Encoder('12345678');
        $claims = ['sub' => 'test', 'name' => '测试 🚀'];
        $token = $encoder->stringify($claims);
        $parsed = $encoder->parse($token, false);
        $this->assertEquals('测试 🚀', $parsed->name);
    }

    public function test_stringify_with_custom_type(): void
    {
        $encoder = new Encoder('12345678');
        $claims = ['sub' => 'test'];
        $token = $encoder->stringify($claims, Algorithm::HS256, 'CustomType');
        // Verify type is in header
        $parts = explode('.', $token);
        $header = json_decode(Encoder::decode($parts[0]), true);
        $this->assertEquals('CustomType', $header['typ']);
    }

    public function test_stringify_with_all_enum_algorithms(): void
    {
        $encoder = new Encoder('12345678');
        $claims = ['sub' => 'test'];

        foreach ([Algorithm::HS256, Algorithm::HS384, Algorithm::HS512] as $alg) {
            $token = $encoder->stringify($claims, $alg);
            $parsed = $encoder->parse($token, false);
            $this->assertEquals('test', $parsed->sub);
        }
    }

    public function test_stringify_algorithm_not_in_allowed_list(): void
    {
        // Create encoder with only HS256 allowed
        $encoder = new Encoder('12345678', [Algorithm::HS256]);
        $this->expectException(UnsupportedAlgorithmException::class);
        $encoder->stringify(['sub' => 'test'], 'HS384');
    }

    public function test_stringify_with_enum_algorithm_not_in_allowed_list(): void
    {
        $encoder = new Encoder('12345678', [Algorithm::HS256]);
        $this->expectException(UnsupportedAlgorithmException::class);
        $encoder->stringify(['sub' => 'test'], Algorithm::HS384);
    }

    // Constructor edge cases

    public function test_constructor_with_single_string_algorithm(): void
    {
        $encoder = new Encoder('12345678', 'HS256');
        $token = $encoder->stringify(['sub' => 'test'], 'HS256');
        $parsed = $encoder->parse($token, false);
        $this->assertEquals('test', $parsed->sub);
    }

    public function test_constructor_with_single_enum_algorithm(): void
    {
        $encoder = new Encoder('12345678', Algorithm::HS256);
        $token = $encoder->stringify(['sub' => 'test'], Algorithm::HS256);
        $parsed = $encoder->parse($token, false);
        $this->assertEquals('test', $parsed->sub);
    }

    public function test_constructor_with_array_of_string_algorithms(): void
    {
        $encoder = new Encoder('12345678', ['HS256', 'HS384']);
        $token = $encoder->stringify(['sub' => 'test'], 'HS384');
        $parsed = $encoder->parse($token, false);
        $this->assertEquals('test', $parsed->sub);
    }

    public function test_constructor_with_array_of_enum_algorithms(): void
    {
        $encoder = new Encoder('12345678', [Algorithm::HS256, Algorithm::HS384]);
        $token = $encoder->stringify(['sub' => 'test'], Algorithm::HS384);
        $parsed = $encoder->parse($token, false);
        $this->assertEquals('test', $parsed->sub);
    }

    public function test_constructor_with_mixed_algorithm_array(): void
    {
        $encoder = new Encoder('12345678', [Algorithm::HS256, 'HS384']);
        $token = $encoder->stringify(['sub' => 'test'], 'HS384');
        $parsed = $encoder->parse($token, false);
        $this->assertEquals('test', $parsed->sub);
    }

    // Assert edge cases

    public function test_assert_with_no_claims(): void
    {
        $encoder = new Encoder('');
        $claims = (object) [];
        // Should not throw
        $encoder->assert($claims);
        $this->assertTrue(true);
    }

    public function test_assert_exp_at_boundary(): void
    {
        $encoder = new Encoder('');
        $claims = (object) ['exp' => time()];
        $this->expectException(TokenExpiredException::class);
        $encoder->assert($claims);
    }

    public function test_assert_iat_at_boundary(): void
    {
        $encoder = new Encoder('');
        $claims = (object) ['iat' => time()];
        // Should not throw (iat <= now is valid)
        $encoder->assert($claims);
        $this->assertTrue(true);
    }

    public function test_assert_nbf_at_boundary(): void
    {
        $encoder = new Encoder('');
        $claims = (object) ['nbf' => time()];
        // Should not throw (nbf <= now is valid)
        $encoder->assert($claims);
        $this->assertTrue(true);
    }

    public function test_assert_with_only_exp(): void
    {
        $encoder = new Encoder('');
        $claims = (object) ['exp' => time() + 3600];
        $encoder->assert($claims);
        $this->assertTrue(true);
    }

    public function test_assert_with_only_iat(): void
    {
        $encoder = new Encoder('');
        $claims = (object) ['iat' => time() - 3600];
        $encoder->assert($claims);
        $this->assertTrue(true);
    }

    public function test_assert_with_only_nbf(): void
    {
        $encoder = new Encoder('');
        $claims = (object) ['nbf' => time() - 3600];
        $encoder->assert($claims);
        $this->assertTrue(true);
    }

    public function test_assert_with_all_three_claims_valid(): void
    {
        $encoder = new Encoder('');
        $now = time();
        $claims = (object) [
            'exp' => $now + 3600,
            'iat' => $now - 3600,
            'nbf' => $now - 1800,
        ];
        $encoder->assert($claims);
        $this->assertTrue(true);
    }

    // Encode/Decode roundtrip tests

    public function test_encode_decode_roundtrip(): void
    {
        $testStrings = [
            'simple',
            'with spaces',
            'with-special-chars!@#$%',
            'with/unicode/测试',
            'very long string '.str_repeat('x', 1000),
            '',
        ];

        foreach ($testStrings as $original) {
            $encoded = Encoder::encode($original);
            $decoded = Encoder::decode($encoded);
            $this->assertEquals($original, $decoded, "Failed for: $original");
        }
    }

    public function test_encode_decode_with_padding(): void
    {
        // Test strings that require different padding amounts
        $testCases = [
            'a' => 3,      // 1 char = 1 byte = 3 padding
            'ab' => 2,    // 2 chars = 2 bytes = 2 padding
            'abc' => 1,   // 3 chars = 3 bytes = 1 padding
            'abcd' => 0,  // 4 chars = 4 bytes = 0 padding
        ];

        foreach ($testCases as $original => $expectedPadding) {
            $encoded = Encoder::encode($original);
            $decoded = Encoder::decode($encoded);
            $this->assertEquals($original, $decoded);
        }
    }

    // Integration tests

    public function test_stringify_and_parse_roundtrip(): void
    {
        $encoder = new Encoder('secret-key');
        $originalClaims = [
            'sub' => 'user123',
            'name' => 'John Doe',
            'iat' => time(),
            'exp' => time() + 3600,
            'custom' => ['nested' => 'value'],
        ];

        $token = $encoder->stringify($originalClaims);
        $parsed = $encoder->parse($token, false);

        $this->assertEquals($originalClaims['sub'], $parsed->sub);
        $this->assertEquals($originalClaims['name'], $parsed->name);
        $this->assertEquals($originalClaims['custom']['nested'], $parsed->custom->nested);
    }

    public function test_stringify_and_parse_with_different_algorithms(): void
    {
        $encoder = new Encoder('secret-key');
        $claims = ['sub' => 'test'];

        foreach (['HS256', 'HS384', 'HS512'] as $alg) {
            $token = $encoder->stringify($claims, $alg);
            $parsed = $encoder->parse($token, false);
            $this->assertEquals('test', $parsed->sub);
        }
    }

    public function test_parse_token_with_extra_dots(): void
    {
        // Token with more than 3 parts - explode limit=3 should still work
        $encoder = new Encoder('12345678');
        $validToken = $encoder->stringify(['sub' => 'test']);
        // Adding extra dots after signature makes it invalid base64
        $tokenWithExtra = $validToken.'.extra.parts';

        // Should fail because signature part becomes invalid base64
        $this->expectException(InvalidTokenException::class);
        $encoder->parse($tokenWithExtra, false);
    }

    // Algorithm enum tests

    public function test_algorithm_values(): void
    {
        $values = Algorithm::values();
        $this->assertIsArray($values);
        $this->assertContains('HS256', $values);
        $this->assertContains('HS384', $values);
        $this->assertContains('HS512', $values);
        $this->assertCount(3, $values);
    }

    public function test_algorithm_hash_methods(): void
    {
        $this->assertEquals('SHA256', Algorithm::HS256->hashMethod());
        $this->assertEquals('SHA384', Algorithm::HS384->hashMethod());
        $this->assertEquals('SHA512', Algorithm::HS512->hashMethod());
    }

    // Test sign method with unsupported algorithm (not in enum)

    public function test_sign_with_unsupported_algorithm_string(): void
    {
        // Create encoder that allows a string algorithm not in enum
        // This tests the Algorithm::tryFrom() === null path
        $encoder = new Encoder('12345678', ['INVALID_ALG']);
        $this->expectException(UnsupportedAlgorithmException::class);
        $this->expectExceptionMessage("Signature algorithm 'INVALID_ALG' is not supported.");
        // Use reflection to call protected sign method
        $reflection = new \ReflectionClass($encoder);
        $method = $reflection->getMethod('sign');
        $method->setAccessible(true);
        $method->invoke($encoder, 'payload', 'INVALID_ALG');
    }

    // Clock tests

    public function test_assert_with_clock(): void
    {
        if (!class_exists(\Samay\FrozenClock::class)) {
            $this->markTestSkipped('vaibhavpandeyvpz/samay is not installed');
        }

        $clockTime = new \DateTimeImmutable('2024-01-01 12:00:00');
        $clock = new \Samay\FrozenClock($clockTime);
        $encoder = new Encoder('12345678', [Algorithm::HS256], $clock);

        $clockTimestamp = $clockTime->getTimestamp();

        // Claims valid at the clock time
        $claims = (object) [
            'sub' => 'user123',
            'exp' => $clockTimestamp + 3600, // 1 hour later
            'iat' => $clockTimestamp - 3600, // 1 hour earlier
            'nbf' => $clockTimestamp - 7200, // 2 hours earlier
        ];

        $encoder->assert($claims);
        $this->assertTrue(true);
    }

    public function test_assert_expired_with_clock(): void
    {
        if (!class_exists(\Samay\FrozenClock::class)) {
            $this->markTestSkipped('vaibhavpandeyvpz/samay is not installed');
        }

        $clockTime = new \DateTimeImmutable('2024-01-01 12:00:00');
        $clock = new \Samay\FrozenClock($clockTime);
        $encoder = new Encoder('12345678', [Algorithm::HS256], $clock);

        $clockTimestamp = $clockTime->getTimestamp();

        // Token expired 1 hour ago
        $claims = (object) [
            'sub' => 'user123',
            'exp' => $clockTimestamp - 3600, // 1 hour before clock time
        ];

        $this->expectException(TokenExpiredException::class);
        $encoder->assert($claims);
    }

    public function test_parse_with_clock(): void
    {
        if (!class_exists(\Samay\FrozenClock::class)) {
            $this->markTestSkipped('vaibhavpandeyvpz/samay is not installed');
        }

        $clockTime = new \DateTimeImmutable('2024-01-01 12:00:00');
        $clock = new \Samay\FrozenClock($clockTime);
        $encoder = new Encoder('12345678', [Algorithm::HS256], $clock);

        $clockTimestamp = $clockTime->getTimestamp();

        // Create token with expiration in the future relative to clock
        $claims = [
            'sub' => 'user123',
            'exp' => $clockTimestamp + 3600, // 1 hour later
            'iat' => $clockTimestamp - 3600, // 1 hour earlier
        ];

        $token = $encoder->stringify($claims);
        $parsed = $encoder->parse($token);

        $this->assertEquals('user123', $parsed->sub);
    }

    public function test_parse_expired_token_with_clock(): void
    {
        if (!class_exists(\Samay\FrozenClock::class)) {
            $this->markTestSkipped('vaibhavpandeyvpz/samay is not installed');
        }

        $clockTime = new \DateTimeImmutable('2024-01-01 12:00:00');
        $clock = new \Samay\FrozenClock($clockTime);
        $encoder = new Encoder('12345678', [Algorithm::HS256], $clock);

        $clockTimestamp = $clockTime->getTimestamp();

        // Create token that expired 1 hour ago
        $claims = [
            'sub' => 'user123',
            'exp' => $clockTimestamp - 3600, // 1 hour before clock time (expired)
            'iat' => $clockTimestamp - 7200, // 2 hours before clock time
        ];

        $token = $encoder->stringify($claims);

        $this->expectException(TokenExpiredException::class);
        $encoder->parse($token);
    }

    public function test_clock_fallback_to_system_time(): void
    {
        // Test that without clock, it uses system time
        $encoder = new Encoder('12345678');
        $claims = (object) [
            'exp' => time() + 3600,
            'iat' => time() - 3600,
            'nbf' => time() - 1800,
        ];

        $encoder->assert($claims);
        $this->assertTrue(true);
    }
}

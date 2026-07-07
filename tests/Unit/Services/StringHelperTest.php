<?php

namespace Tests\Unit\Services;

use App\Helpers\StringHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StringHelperTest extends TestCase
{
    public function test_initials_from_name(): void
    {
        $this->assertEquals('JD', StringHelper::initials('John Doe'));
        $this->assertEquals('AJ', StringHelper::initials('Andi Jaya'));
        $this->assertEquals('JDM', StringHelper::initials('John Doe Mayer', 3));
    }

    public function test_initials_with_limit(): void
    {
        $this->assertEquals('JD', StringHelper::initials('John Doe Smith', 2));
        $this->assertEquals('J', StringHelper::initials('John', 1));
    }

    public function test_format_whatsapp_number(): void
    {
        // With 0 prefix
        $this->assertEquals('628123456789', StringHelper::formatWhatsAppNumber('08123456789'));

        // With international format
        $this->assertEquals('628123456789', StringHelper::formatWhatsAppNumber('+628123456789'));

        // Already with 62
        $this->assertEquals('628123456789', StringHelper::formatWhatsAppNumber('628123456789'));

        // With dashes
        $this->assertEquals('628123456789', StringHelper::formatWhatsAppNumber('628-123-456-789'));
    }

    public function test_mask_phone(): void
    {
        $this->assertEquals('****6789', StringHelper::maskPhone('08123456789'));
        $this->assertEquals('-', StringHelper::maskPhone(null));
        $this->assertEquals('123', StringHelper::maskPhone('123'));
    }

    public function test_truncate(): void
    {
        $text = 'Lorem ipsum dolor sit amet';

        $this->assertEquals('Lorem ipsum...', StringHelper::truncate($text, 12));
        $this->assertEquals('Lorem ipsum dolor sit amet', StringHelper::truncate($text, 100));
    }

    public function test_slug(): void
    {
        $this->assertEquals('hello-world', StringHelper::slug('Hello World'));
        $this->assertEquals('test-page-url', StringHelper::slug('Test Page-URL!'));
    }

    public function test_format_rupiah(): void
    {
        $this->assertEquals('Rp 1.000.000', StringHelper::formatRupiah(1000000));
        $this->assertEquals('1.000.000', StringHelper::formatRupiah(1000000, false));
        $this->assertEquals('Rp 500', StringHelper::formatRupiah(500.50));
    }

    public function test_parse_rupiah(): void
    {
        $this->assertEquals(1000000, StringHelper::parseRupiah('Rp 1.000.000'));
        $this->assertEquals(500000, StringHelper::parseRupiah('500.000'));
    }

    public function test_format_nisn(): void
    {
        $this->assertEquals('0000123456', StringHelper::formatNisn('123456'));
        $this->assertEquals('-', StringHelper::formatNisn(null));
    }

    public function test_clean_name(): void
    {
        $this->assertEquals('John Doe', StringHelper::cleanName('  john   doe  '));
        $this->assertEquals('John Doe', StringHelper::cleanName('JOHN DOE'));
    }

    public function test_is_valid_phone(): void
    {
        $this->assertTrue(StringHelper::isValidPhone('08123456789'));
        $this->assertTrue(StringHelper::isValidPhone('628123456789'));
        $this->assertFalse(StringHelper::isValidPhone('123'));
        $this->assertFalse(StringHelper::isValidPhone(null));
    }

    public function test_random_string(): void
    {
        $alphanumeric = StringHelper::random(16);
        $numeric = StringHelper::random(10, 'numeric');
        $hex = StringHelper::random(8, 'hex');

        $this->assertEquals(16, strlen($alphanumeric));
        $this->assertEquals(10, strlen($numeric));
        $this->assertEquals(8, strlen($hex));
        $this->assertMatchesRegularExpression('/^[0-9]+$/', $numeric);
        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $hex);
    }

    public function test_title_case_indonesian(): void
    {
        $this->assertEquals('Di Jakarta', StringHelper::titleCase('di jakarta'));
        $this->assertEquals('Nama Dan Alamat', StringHelper::titleCase('nama dan alamat'));
    }
}

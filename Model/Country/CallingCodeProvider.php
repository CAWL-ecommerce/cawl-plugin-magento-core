<?php

namespace Cawl\PaymentCore\Model\Country;

use Magento\Framework\Exception\LocalizedException;

class CallingCodeProvider
{
    private const MIN_LENGTH = 5;
    private const MAX_LENGTH = 15;
    /**
     * @var array|null
     */
    private $countryData = null;

    /**
     * Get E164 calling code for a country
     *
     * @param string $countryCode ISO2 country code (e.g., 'US', 'RS')
     * @return string|null E164 code (e.g., '1', '381') or null if not found
     */
    public function getCallingCode(string $countryCode): ?string
    {
        $data = $this->getCountryData();
        $countryCode = strtoupper($countryCode);

        return isset($data[$countryCode]) ? $data[$countryCode]['e164'] : null;
    }

    /**
     * Format phone number to E164 format
     *
     * @param string $phoneNumber Phone number in any format
     * @param string $countryCode ISO2 country code
     * @return string Formatted phone number in E164 format (e.g., '+41446681800')
     * @throws LocalizedException
     */
    public function formatE164(string $phoneNumber, string $countryCode): string
    {
        $callingCode = $this->getCallingCode($countryCode);

        if ($callingCode === null) {
            throw new LocalizedException(__('Unsupported or missing country code for %1.', $countryCode));
        }

        $digits = preg_replace('/\D+/', '', $phoneNumber);

        if (strpos($digits, $callingCode) === 0) {
            return '+' . $digits;
        }

        $digits = ltrim($digits, '0');

        $isValid = $this->isValidPhoneNumberE164($digits);

        if ($isValid === false) {
            throw new LocalizedException(
                __(
                    'Invalid phone number for %1. Expected %2-%3 characters.',
                    $countryCode,
                    self::MIN_LENGTH,
                    self::MAX_LENGTH
                )
            );
        }

        return '+' . $callingCode . $digits;
    }

    /**
     * Validate if a phone number is valid
     * Basic validation - checks if number has reasonable length
     *
     * @param string $phoneNumber Phone number
     * @return bool
     */
    public function isValidPhoneNumberE164(string $phoneNumber): bool
    {
        $numericOnly = preg_replace('/[^0-9]/', '', $phoneNumber);
        $length = strlen($numericOnly);

        return $length >= self::MIN_LENGTH && $length <= self::MAX_LENGTH;
    }

    private function getCountryData(): array
    {
        if ($this->countryData === null) {
            $this->countryData = CountryData::getData();
        }

        return $this->countryData;
    }
}

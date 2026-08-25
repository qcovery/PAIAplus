<?php
/**
 * View helper providing selected patron account data to any template.
 *
 * PHP version 8
 *
 * @category VuFind
 * @package  View_Helpers
 * @author   Oliver Stöhr <stoehr@effective-webwork.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
namespace PAIAplus\View\Helper\PAIAplus;

use VuFind\Auth\ILSAuthenticator;
use VuFind\ILS\Connection;

/**
 * Makes the expiration date and the patron status of the currently logged-in patron available
 * in every template (e.g., header.phtml), without the need to pass them through every MyResearch action.
 */
class PatronInfo extends \Laminas\View\Helper\AbstractHelper
{
    /**
     * Format used to display the expiration date.
     *
     * @var string
     */
    public const EXPIRES_DISPLAY_FORMAT = 'd.m.Y';

    /**
     * ILS connection
     *
     * @var Connection
     */
    protected $catalog;

    /**
     * ILS authenticator
     *
     * @var ILSAuthenticator
     */
    protected $ilsAuthenticator;

    /**
     * PAIA configuration
     *
     * @var \Laminas\Config\Config
     */
    protected $config;

    /**
     * Patron data for the current request; null as long as it has not been
     * looked up yet.
     *
     * @var array|null
     */
    protected $patronInfo = null;

    /**
     * Constructor
     *
     * @param Connection             $catalog          ILS connection
     * @param ILSAuthenticator       $ilsAuthenticator ILS authenticator
     * @param \Laminas\Config\Config $config           PAIA configuration
     */
    public function __construct(
        Connection $catalog,
        ILSAuthenticator $ilsAuthenticator,
        $config
    ) {
        $this->catalog = $catalog;
        $this->ilsAuthenticator = $ilsAuthenticator;
        $this->config = $config;
    }

    /**
     * Get all available patron information at once.
     *
     * @return array Array with the patron information; the values are
     * empty strings if no patron is logged in or the ILS is not reachable.
     */
    public function getPatronInfo()
    {
        if ($this->patronInfo === null) {
            $this->patronInfo = [
                'expires' => '',
                'expiresRaw' => '',
                'status' => '',
            ];
            try {
                $patron = $this->ilsAuthenticator->storedCatalogLogin();
                if (is_array($patron)) {
                    $profile = $this->catalog->getMyProfile($patron);
                    $this->patronInfo = [
                        // Display date as formatted by the ILS driver ...
                        'expires' => $profile['expires'] ?? '',
                        // and the unformatted value as delivered by PAIA,
                        // which can be compared to the current date.
                        'expiresRaw' => $patron['expires'] ?? '',
                        'status' => $patron['status'] ?? '',
                    ];
                }
            } catch (\Exception $e) {
                // ILS not available -- behave as if no data had been found.
            }
        }
        return $this->patronInfo;
    }

    /**
     * Get the expiration date of the patron account, formatted for display.
     *
     * @return string
     */
    public function getExpirationDateFormatted()
    {
        $expiryDate = $this->getExpirationDate();
        return $expiryDate === null
            ? $this->getPatronInfo()['expires']
            : $expiryDate->format(self::EXPIRES_DISPLAY_FORMAT);
    }

    /**
     * Get the expiration date of the patron account as a date object, based on the
     * unformatted value delivered by PAIA.
     *
     * @return \DateTimeImmutable|null Null if the date is missing or unparseable
     */
    protected function getExpirationDate()
    {
        $expires = $this->getPatronInfo()['expiresRaw'];
        if (empty($expires)) {
            return null;
        }
        try {
            return new \DateTimeImmutable($expires);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get an additional CSS class describing how close the patron account is to
     * its expiration date: "alert-danger" if the account has already expired,
     * "alert-warning" if it expires within the number of days configured in expiration_warning_days (set
     * in PAIA.ini, 0 disables the class).
     *
     * @return string Class name or an empty string
     */
    public function getExpirationClass()
    {
        $expiryDate = $this->getExpirationDate();
        if ($expiryDate === null) {
            return '';
        }
        $today = new \DateTimeImmutable('today');
        if ($expiryDate < $today) {
            return 'alert-danger';
        }
        $days = (int)($this->config['PAIA']['expiration_warning_days'] ?? 30);
        if ($days > 0 && $expiryDate < $today->modify('+' . $days . ' days')) {
            return 'alert-warning';
        }
        return '';
    }

    /**
     * Get the status of the patron account.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getPatronInfo()['status'];
    }

    /**
     * Should the patron status be displayed on all account pages?
     *
     * @return bool
     */
    public function showStatus()
    {
        return !empty($this->config['PAIA']['show_patron_status_on_all_pages']);
    }
}

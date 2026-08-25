<?php
/**
 * PatronInfo helper factory.
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

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * PatronInfo helper factory.
 */
class PatronInfoFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param ContainerInterface $container     Service manager
     * @param string             $requestedName Service being created
     * @param null|array         $options       Extra options (optional)
     *
     * @return object
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ) {
        if (!empty($options)) {
            throw new \Exception('Unexpected options sent to factory.');
        }
        return new $requestedName(
            $container->get(\VuFind\ILS\Connection::class),
            $container->get(\VuFind\Auth\ILSAuthenticator::class),
            $container->get(\VuFind\Config\PluginManager::class)->get('PAIA')
        );
    }
}

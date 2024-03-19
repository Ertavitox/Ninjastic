<?php

namespace App\Twig;

use App\Helper\FlashBag;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Extension\GlobalsInterface;
use Twig\TwigTest;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getName()
    {
        return 'AppExtension';
    }

    public function getGlobals(): array
    {
        $session = new Session();
        return [
            'GET' => $_GET,
            'POST' => $_POST,
            'flashbag' => new FlashBag(),
            'session' => $session,
            'apps' => [
                'baseUrl' => $this->getBaseUrl(),
                'adminUrl' => $this->getAdminUrl(),
                "tableLength" => [
                    "10" => 10,
                    "25" => 25,
                    "50" => 50,
                    "100" => 100,
                ],
            ],
        ];
    }

    private function getBaseUrl(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $scheme = $request->getScheme();
        $host = $request->getHttpHost();
        return $scheme . '://' . $host;
    }

    private function getAdminUrl(): string
    {

        return $this->getBaseUrl() . "/admin";
    }



    public function getTests(): array
    {
        return [
            new TwigTest('numeric', [$this, 'isNumeric']),
            new TwigTest('number', [$this, 'isNumeric']),
        ];
    }

    public function isNumeric($value): bool
    {
        return is_numeric($value);
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getHostNameMain', array($this, 'getHostNameMain'), array('is_safe' => array('html'))),
            new TwigFunction('pathUrl', array($this, 'pathUrl'), array('is_safe' => array('html'))),
            new TwigFunction('checkStayPage', array($this, 'checkStayPage'), array('is_safe' => array('html'))),
        ];
    }

    public static function checkStayPage()
    {
        if (isset($_POST['stay']) && $_POST['stay'] == 1) {
            return false;
        }
        return true;
    }

    public static function adminPager($query, $currentPage = 1, $limit = 25)
    {
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pageCount = ceil($totalItems / $limit);
        $offset = ($limit * ($currentPage - 1));

        $paginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return array(
            'EntityList' => $paginator,
            'totalItems' => $totalItems,
            'pageCount' => $pageCount,
            'currentPage' => $currentPage,
            'limit' => $limit,
            'offset' => $offset,
            'query' => $_SERVER['QUERY_STRING']
        );
    }

    public function pathUrl($parameters = [])
    {
        $pagenumber = false;
        $pagenumberChange = false;

        $parseArray = array();
        if (isset($parameters['actpage'])) {
            $pagenumber = (int) $parameters['actpage'];
            $pagenumberChange = true;
        }
        $pagesize = false;
        $pagesizeChange = false;
        if (isset($parameters['pagesize'])) {
            $pagesize = (int) $parameters['pagesize'];
            $pagesizeChange = true;
        }
        $orderField = false;
        $orderFieldChange = false;
        $orderSortChange = false;
        if (isset($parameters['orderfield'])) {
            $orderField = $parameters['orderfield'];
            $orderFieldChange = true;
            $orderSortChange = true;
        }

        //Akt QueryString
        if (isset($parameters['q'])) {
            $parseArray = explode('&', $parameters['q']);
            if ($parseArray && is_array($parseArray) && count($parseArray) > 0) {
                foreach ($parseArray as $key => $item) {
                    if (strpos($item, 'actpage') !== false && $pagenumberChange) {
                        $parseArray[$key] = 'actpage=' . $pagenumber;
                        $pagenumberChange = false;
                    }
                    if (strpos($item, 'pagesize') !== false && $pagesizeChange) {
                        $parseArray[$key] = 'pagesize=' . $pagesize;
                        $pagesizeChange = false;
                    }
                    if (strpos($item, 'orderfield') !== false && $orderFieldChange) {
                        $orderFieldParse = explode('=', $item);
                        if (isset($orderFieldParse[1]) && $orderFieldParse[1] == $orderField) {
                            $orderSortChange = true;
                        }
                        $parseArray[$key] = 'orderfield=' . $orderField;
                        $orderFieldChange = false;
                    }
                    if (strpos($item, 'ordersort') !== false && $orderSortChange) {
                        $orderSortParse = explode('=', $item);
                        if (isset($orderSortParse[1]) && $orderSortParse[1] == 'ASC') {
                            $parseArray[$key] = 'ordersort=DESC';
                        } elseif (isset($orderSortParse[1]) && $orderSortParse[1] == 'DESC') {
                            $parseArray[$key] = 'ordersort=ASC';
                        }
                        $orderSortChange = false;
                    }
                }
            }
        }
        if ($pagenumberChange) {
            $pagenumberChange = false;
            $parseArray[] = 'actpage=' . $pagenumber;
        }
        if ($pagesizeChange) {
            $pagesizeChange = false;
            $parseArray[] = 'pagesize=' . $pagesize;
        }
        if ($orderFieldChange) {
            $orderFieldChange = false;
            $parseArray[] = 'orderfield=' . $orderField;
        }
        if ($orderSortChange) {
            $orderSortChange = false;
            $parseArray[] = 'ordersort=ASC';
        }
        $queryString = implode('&', $parseArray);
        return $this->getAdminUrl() . '/index.php?' . $queryString;
    }

    public static function getHostNameMain()
    {
        $host = explode('.', $_SERVER['HTTP_HOST']);
        return strtoupper($host[0]) . ' ADMIN';
    }
}
